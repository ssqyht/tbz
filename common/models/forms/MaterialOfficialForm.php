<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/28
 * Time: 9:39
 */

namespace common\models\forms;

use common\components\traits\ModelAttributeTrait;
use common\models\FileCommon;
use common\components\traits\ModelErrorTrait;
use common\models\FileUsedRecord;
use common\models\MaterialOfficial;
use Monolog\Handler\IFTTTHandler;

class MaterialOfficialForm extends \yii\base\Model
{
    use ModelErrorTrait;
    use ModelAttributeTrait;
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;

    public $id;
    public $user_id;
    public $file_id;
    public $thumbnail;
    public $cid;
    public $tags;
    public $name;
    public $extra_contents;
    public $width;
    public $height;
    public $file_type;
    public $status;

    public function rules()
    {
        return [
            [['file_id', 'cid'], 'integer', 'on' => ['create', 'update']],
            [['extra_contents'], 'string', 'on' => ['create', 'update']],
            [['name'], 'string', 'max' => 50, 'on' => ['create', 'update']],
            [['tags', 'thumbnail'], 'string', 'max' => 255, 'on' => ['create', 'update']],
            [['extra_contents'], 'default', 'value' => '', 'on' => ['create', 'update']],
            [['file_id', 'cid', 'tags'], 'required', 'on' => ['create']],
            ['id', 'required', 'on' => ['update', 'delete']],
            ['status', 'required', 'on' => ['delete']],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['user_id', 'file_id', 'thumbnail', 'cid', 'tags', 'name', 'extra_contents', 'width', 'height', 'file_type'],
            'update' => ['id', 'user_id', 'file_id', 'thumbnail', 'cid', 'tags', 'name', 'extra_contents', 'width', 'height', 'file_type'],
            'delete' => ['id', 'status'],
        ];
    }

    /**
     * 官方素材的添加、修改、删除（假删除）
     * @param $params
     * @return bool|MaterialOfficial|null
     */
    public function editOfficialMaterial($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }
        if ($this->file_id) {
            $file_data = FileCommon::findOne(['file_id' => $this->file_id]);
            if (!$file_data) {
                $this->addError('', '上传的文件不存在');
                return false;
            }
            $this->width = $file_data->width;
            $this->height = $file_data->height;
            $this->file_type = $file_data->type;
            $this->thumbnail = $file_data->path;
        }
        if ($this->id) {
            $model = MaterialOfficial::findOne(['id' => $this->id]);
            if (!$model) {
                $this->addError('', '所操作的官方素材不存在');
            }
        } else {
            $model = new MaterialOfficial();
        }
        $model->load($this->getUpdateAttributes(), '');
        $model->user_id = \Yii::$app->user->id;
        //添加素材时
        if ($model->isNewRecord) {
            $create_file = $model->file_id;
        }
        //修改素材的引用文件时
        if ($model->isAttributeChanged('thumbnail') && $model->isAttributeChanged('file_id')) {
            $drop_file = $model->getOldAttribute('file_id');
            $create_file = $model->file_id;
            $old_key = $model->oldPrimaryKey;
        }
        //删除素材时（假删除）
        if ($model->status && $model->status == static::RECYCLE_BIN_STATUS) {
            $drop_file = $model->getOldAttribute('file_id');
            $old_key = $model->oldPrimaryKey;
        }
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $purpose = FileUsedRecord::PURPOSE_MATERIAL_OFFICIAL;
            if (!($model->validate() && $model->save())) {
                throw new \Exception('官方素材操作失败' . $model->getStringErrors());
            }
            //删除文件引用记录
            if ($drop_file) {
                $result = FileUsedRecord::dropRecord($drop_file, $purpose, $old_key);
                if (!$result || (is_object($result) && $result->getErrors())) {
                    throw new \Exception('删除官方素材引用文件记录失败' . (is_object($result) ? $result->getStringErrors() : ''));
                }
            }
            //添加文件引用记录
            if ($create_file) {
                $file_result = FileUsedRecord::createRecord(\Yii::$app->user->id, $create_file, $purpose, $model->primaryKey);
                if (!$file_result || (is_object($file_result) && $file_result->getErrors())) {
                    throw new \Exception('创建官方素材引用文件记录失败' . (is_object($file_result) ? $file_result->getStringErrors() : ''));
                }
            }
            $transaction->commit();
            return $model;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('', $e->getMessage());
            return false;
        }
    }
}