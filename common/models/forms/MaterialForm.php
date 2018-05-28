<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 18:11
 */
namespace common\models\forms;
use common\models\MaterialMember;
use common\components\traits\ModelErrorTrait;
use common\models\MaterialTeam;

;
class MaterialForm extends \yii\base\Model
{
    use ModelErrorTrait;
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
    public $method;

    private $_user;

    public function rules()
    {
        return [
            [['user_id', 'folder_id', 'file_id', 'mode','team_id'], 'integer'],
            [['file_name', 'thumbnail'], 'string', 'max' => 255],
            [['folder_id'],'default','value'=>0],
            ['method','required'],
            ['method', 'in', 'range' => [static::MATERIAL_MEMBER, static::MATERIAL_TEAM ]],
        ];
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
     * 获取用户id
     */
    public function getUser(){
        if ($this->_user === null){
            $this->_user =1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }
}