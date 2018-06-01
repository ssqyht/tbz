<?php

namespace common\models\forms;

use common\models\FileCommon;
use common\models\FileUsedRecord;
use common\models\TbzSubject;
use yii\helpers\Json;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;
use common\components\traits\ModelAttributeTrait;

class TbzSubjectForm extends Model
{
    use ModelErrorTrait;
    use ModelAttributeTrait;
    /* @var integer 回收站状态 */
    const DELETE_STATUS = 7;

    public $id;
    public $sort;
    public $title;
    public $description;
    public $seo_keyword;
    public $seo_description;
    public $thumbnail;
    public $seo_title;
    public $banner;
    public $status;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['sort', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner'], 'required', 'on' => 'create'],
            [['sort', 'thumbnail', 'banner'], 'integer', 'on' => ['create', 'update']],
            ['title', 'string', 'max' => 150, 'on' => ['create', 'update']],
            [['description', 'seo_keyword', 'seo_description'], 'string', 'max' => 255, 'on' => ['create', 'update']],
            [['seo_title'], 'string', 'max' => 100, 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['delete', 'update']],
            ['status', 'required', 'on' => 'delete'],
            ['thumbnail', function () {
                if (!($this->isFile($this->thumbnail))) {
                    $this->addError('thumbnail', '缩略图文件不存在');
                }
            }, 'on' => ['create', 'update']],
            ['banner', function () {
                if (!($this->isFile($this->banner))) {
                    $this->addError('banner', 'banner图文件不存在');
                }
            }, 'on' => ['create', 'update']]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'create' => ['sort', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner', 'status'],
            'update' => ['sort', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner', 'status', 'id'],
            'delete' => ['id', 'status'],
        ];
    }

    /**
     * @param $params
     * @return bool|TbzSubject|null
     */
    public function submit($params)
    {

        if (!$this->load($params, '')) {
            return false;
        }
        if (!$this->validate()) {
            return false;
        }
        if ($this->id) {
            $model = TbzSubject::findOne($this->id);
            if (!$model) {
                $this->addError('id', '资源不存在');
                return false;
            }
        } else {
            $model = new TbzSubject();
        }
        $model->load($this->getUpdateAttributes(), '');
        //保存模板专题数据
        $purpose = FileUsedRecord::PURPOSE_CLASSIFY;
        $drop_data = [];
        $create_data = [];
        //创建新的模板专题时的操作
        if ($model->isNewRecord) {
            $create_data['banner'] = $model->banner;
            $create_data['thumbnail'] = $model->thumbnail;
        }else{
            /** 修改和删除时的操作 */
            //缩略图有变化时的操作
            if ($model->isAttributeChanged('thumbnail')) {
                $drop_data['thumbnail'] = $model->getOldAttribute('thumbnail');
                $create_data['thumbnail'] = $model->thumbnail;
            }
            //banner图有变化时的操作
            if ($model->isAttributeChanged('banner')) {
                $drop_data['banner'] = $model->getOldAttribute('banner');
                $create_data['banner'] = $model->banner;
            }
            //删除模板时的操作
            if ($model->isAttributeChanged('status')) {
                if ($model->status == static::DELETE_STATUS) {
                    $drop_data['thumbnail'] = $model->getOldAttribute('thumbnail');
                    $drop_data['banner'] = $model->getOldAttribute('banner');
                }
            }
        }
        if (!($model->validate() && $model->save())) {
            $this->addErrors($model->getErrors());
            return false;
        }
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            /** 删除文件引用记录 */
            if ($drop_data) {
                foreach ($drop_data as $key => $value) {
                    if ($model->primaryKey) {
                        $result = FileUsedRecord::dropRecord(\Yii::$app->user->id, $value, $purpose, $model->oldPrimaryKey);
                        if (!$result || (is_object($result) && $result->getErrors())) {
                            throw new \Exception('删除' . $key . '引用文件记录失败'.(is_object($result) ? $result->getStringErrors() : ''));
                        }
                    }
                }
            }
            /** 创建文件引用记录 */
            if ($create_data) {
                foreach ($create_data as $key => $value) {
                    $result = FileUsedRecord::createRecord(\Yii::$app->user->id, $value, $purpose, $model->primaryKey);
                    if (!$result  ||  (is_object($result) && $result->getErrors())) {
                        throw new \Exception('创建' . $key . '引用文件记录失败'.(is_object($result) ? $result->getStringErrors() : ''));
                    }
                }
            }
            $transaction->commit();
            return $model;
        } catch (\Throwable $e) {
            $this->addError('',$e->getMessage());
            return false;
        }
    }

    /**
     * 判断文件是否存在
     * @param $id
     * @return bool
     */
    public function isFile($id)
    {
        if (!FileCommon::findOne(['file_id' => $id])) {
            return false;
        }
        return true;
    }
}