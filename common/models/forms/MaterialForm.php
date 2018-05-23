<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 18:11
 */
namespace common\models\forms;
use common\models\MaterialMember;
use common\models\MaterialTeam;
use common\components\traits\ModelErrorTrait;;
class MaterialForm extends \yii\base\Model
{
    use ModelErrorTrait;
    /** @var string 个人素材 */
    const MATERIAL_MEMBER = 'material_member';
    /** @var string 团队素材 */
    const MATERIAL_TEAM = 'material_team';
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;
    public $source;
    public $sid;
    public $team_id;
    public $user_id;
    public $session_id;
    public $filename;
    public $old_name;
    public $title;
    public $width;
    public $height;
    public $size;
    public $status;
    public $folder_id;
    public $method;
    public function rules()
    {
        return [
            [['source', 'sid', 'team_id', 'user_id', 'width', 'height', 'size', 'status', 'folder_id'], 'integer'],
            [['session_id'], 'string', 'max' => 30],
            [['filename', 'old_name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 60],
            ['method','required'],
            ['method', 'in', 'range' => [static::MATERIAL_MEMBER, static::MATERIAL_TEAM ]],
        ];
    }

    /**
     * 添加个人或者团队素材
     * @return bool|MaterialMember|MaterialTeam
     */
    public function addMaterial()
    {
        if (!$this->validate()) {
            return false;
        }
        if ($this->method == 'material_member'){
            $model = new MaterialMember();
        }else{
            $model = new MaterialTeam();
        }
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        return false;
    }

    /**
     * 修改素材
     * @param $id
     * @return bool|MaterialMember|MaterialTeam|null
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
            $model = MaterialMember::findOne($id);
        }else{
            $model =MaterialTeam::findOne($id);
        }
        if (!$model) {
            $this->addError('', '该素材不存在');
            return false;
        }
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
            $model = MaterialMember::findOne($id);
        }else{
            $model =MaterialTeam::findOne($id);
        }
        if (!$model) {
            $this->addError('id', '该素材不存在');
        }
        $model->status = 7;
        if ($model->save(false)) {
            return true;
        }
        $this->addError('', '删除失败');
        return false;
    }
}