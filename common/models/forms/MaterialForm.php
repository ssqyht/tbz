<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 18:11
 */
namespace common\models\forms;

use common\components\traits\ModelAttribute;
use common\models\FileUsedRecord;
use Yii;
use common\models\MaterialMember;
use common\components\traits\ModelErrorTrait;
use common\models\MaterialTeam;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * Class MaterialForm
 * @property MaterialTeam|MaterialMember|null $activeModel
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class MaterialForm extends \yii\base\Model
{
    use ModelErrorTrait;
    use ModelAttribute;

    /** @var string 个人素材 */
    const MATERIAL_MEMBER = 'material_member';
    /** @var string 团队素材 */
    const MATERIAL_TEAM = 'material_team';

    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;

    public $file_id;
    public $thumbnail;
    public $team_id;
    public $user_id;
    public $mode;
    public $file_name;
    public $folder_id;
    public $id;

    private $_user;
    private $_activeModel;

    public function rules()
    {
        return [
            [['thumbnail', 'file_id'], 'required', 'when' => function($model){
                return empty($model->id);
            }],
            [['folder_id', 'file_id', 'team_id', 'id'], 'integer'],
            [['file_name', 'thumbnail'], 'string', 'max' => 255],
            ['id', function(){
                if (empty($this->activeModel)) {
                    $this->addError('id', '请求资源不存在');
                }
            }]
        ];
    }

    /**
     * 用户素材处理函数
     * @param $params
     * @return bool|MaterialMember|MaterialTeam|null
     * @author thanatos <thanatos915@163.com>
     */
    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }

        $model = $this->activeModel;
        $model->load($this->getUpdateAttributes(), '');
        // 验证数据
        if (!$model->validate())
            return false;

        $model->user_id = Yii::$app->user->id;
        // 添加Team信息
        if ($model instanceof MaterialTeam)
            $model->team_id = Yii::$app->user->identity->team->id;

        $transaction = Yii::$app->getDb()->beginTransaction();

        // 保存素材信息
        if (!($model->validate() && $model->save())) {
            $this->addErrors($model->getErrors());
            return false;
        }

        try {
            $purpose = FileUsedRecord::PURPOSE_MATERIAL;
            // 处理素材源文件信息, 如果文件变化了。则处理文件引用信息
            if ($model->isAttributeChanged('thumbnail') && $model->isAttributeChanged('file_id')) {
                // 处理修改素材文件流程
                if ($model->primaryKey) {
                    // 删除原来的文件引用信息
                    $file_id = $model->getOldAttribute('file_id');
                    if (!$result = FileUsedRecord::dropRecord($model->user_id, $file_id, $purpose, $model->oldPrimaryKey)) {
                        throw new Exception('Drop old File Use failed'. $result->getStringErrors());
                    }
                }
            }

            // 增加文件引用记录
            if (!$result = FileUsedRecord::createRecord($model->user_id, $model->file_id, $purpose, $model->primaryKey)) {
                throw new \Exception('Create File Use failed'. $result->getStringErrors());
            }
            $transaction->commit();
            return $model;
        } catch (\Throwable $e) {
            try {
                $transaction->rollBack();
            } catch (\Throwable $e) {}
            $message = $e->getMessage();
            // 添加错误信息
            if (strpos($message, '=') === false)
                $this->addError('', $message);
            else
                $this->addErrors(Json::decode(explode(':', $message)[1]));
            return false;
        }

    }

    /**
     * 添加个人或者团队素材
     * @return bool|MaterialMember|False
     */
    public function addMaterial()
    {
        if (!$this->validate()) {
            return false;
        }
        if ($this->method == 'material_member'){
            //个人
            $model = new MaterialMember();
        }else{
            //团队
            $model = new MaterialTeam();
        }
        $this->user_id = $this->user;
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        return false;
    }

    /**
     * 修改素材
     * @param $id
     * @return bool|MaterialMember|False|null
     */
    public function updateMaterial($id)
    {
        if (!$id) {
            $this->addError('id', '唯一标识不能为空');
            return false;
        }
        if (!$this->validate()) {
            return false;
        }
        if ($this->method == 'material_member'){
            //个人
            $model = MaterialMember::findOne(['id'=>$id,'user_id'=>$this->user]);
        }else{
            //团队
            $model =MaterialTeam::findOne(['id'=>$id,'team_id'=>$this->team_id]);
        }
        if (!$model) {
            $this->addError('', '该素材不存在');
            return false;
        }
        $this->user_id = $this->user;
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        $this->addError('', '修改失败');
        return false;
    }
    /**
     * 把素材放入回收站
     * @param $id
     * @return bool
     */
    public function deleteMaterial($id)
    {
        if ($this->method == 'material_member'){
            $model = MaterialMember::findOne(['id'=>$id,'user_id'=>$this->user]);
        }else{
            $model =MaterialTeam::findOne(['id'=>$id,'team_id'=>$this->team_id]);
        }
        if (!$model) {
            $this->addError('id', '该素材不存在');
            return false;
        }
        $model->status = static::RECYCLE_BIN_STATUS;
        if ($model->save(false)) {
            return true;
        }
        $this->addError('', '删除失败');
        return false;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     * @author thanatos <thanatos915@163.com>
     */
    public function getActiveModel()
    {
        if ($this->_activeModel === null) {
            $user = Yii::$app->user->identity;
            /** @var MaterialMember|MaterialTeam $modelClass */
            $modelClass = '';
            if ($user->team) {
                $modelClass = MaterialTeam::class;
            } else {
                $modelClass = MaterialMember::class;
            }

            if ($this->id) {
                $model = $modelClass::findById($this->id);
            } else {
                $model = new $modelClass();
            }
            $this->_activeModel = $model;
        }
        return $this->_activeModel;
    }

    /**
     * 获取用户id
     */
    public function getUser(){
        if ($this->_user === null){
            $this->_user =1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }
}