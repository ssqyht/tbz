<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/28
 * Time: 9:39
 */

namespace common\models\forms;

use common\models\FileCommon;
use common\components\traits\ModelErrorTrait;
use common\models\MaterialOfficial;
class MaterialOfficialForm extends \yii\base\Model
{
    use ModelErrorTrait;
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;

    public $file_id;
    public $thumbnail;
    public $cid;
    public $user_id;
    public $tags;
    public $name;
    public $extra_contents;
    public $width;
    public $height;
    public $file_type;


    private $_user;

    public function rules()
    {
        return [
            [['file_id', 'cid'], 'integer'],
            [['extra_contents'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['tags', 'thumbnail'], 'string', 'max' => 255],
            [['extra_contents'], 'default', 'value' => ''],
            [['file_id', 'cid', 'name'], 'required'],
        ];
    }

    /**
     * 添加官方素材
     * @return bool|MaterialOfficial
     */
    public function addMaterial()
    {
        if (!$this->validate()) {
            return false;
        }
        $model = new MaterialOfficial();
        if (!$this->file) {
            return false;
        }
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        return false;
    }

    /**
     * 修改官方素材
     * @param $id
     * @return bool|MaterialOfficial|null
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
        $model = MaterialOfficial::findOne(['id' => $id]);
        if (!$model) {
            $this->addError('', '该素材不存在');
            return false;
        }
        if (!$this->file) {
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
        $model = MaterialOfficial::findOne(['id' => $id]);
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
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = 1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }

    /**
     * @return bool
     */
    public function getFile()
    {
        $file_data = FileCommon::findOne(['file_id' => $this->file_id]);
        if (!$file_data) {
            $this->addError('', '上传的文件不存在');
            return false;
        }
        $this->width = $file_data->width;
        $this->height = $file_data->height;
        $this->file_type = $file_data->type;
        $this->thumbnail = $file_data->path;
        if (!$this->user) {
            $this->addError('', '获取用户信息失败，请登录');
            return false;
        }
        $this->user_id = $this->user;
        return true;
    }
}