<?php

namespace common\models\forms;

use common\models\TbzSubject;
use yii\base\Model;

class TbzSubjectForm extends Model
{
    public $sort;
    public $title;
    public $description;
    public $seo_keyword;
    public $seo_description;
    public $thumbnail;
    public $seo_title;
    public $banner;
    public $status;

    public function rules()
    {
        return [
            [['sort', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner', 'status'], 'required'],
            ['sort', 'integer'],
            ['title', 'string', 'max' => 150],
            [['description', 'seo_keyword', 'seo_description'], 'string', 'max' => 255],
            [['thumbnail', 'seo_title'], 'string', 'max' => 100],
            ['banner', 'string', 'max' => 60],
            ['status', 'string', 'max' => 2],
        ];
    }

    /**
     * 获取所有属性
     * @return array
     */
    private function _getParam()
    {
        return $this->attributes;
    }

    /**
     * @param $params
     * @return bool|TbzSubject
     * 添加模板新消息
     */
    public function TbzSubjectAdd($params)
    {
        if (!$this->validate()) {
            return false;
        }

        $tbz_subject = new TbzSubject();
        $tbz_subject->title = $params['title'];
        $tbz_subject->description = $params['description'];
        $tbz_subject->banner = $params['banner'];
        $tbz_subject->thumbnail = $params['thumbnail'];
        $tbz_subject->seo_title = $params['seo_title'];
        $tbz_subject->seo_keyword = $params['seo_keyword'];
        $tbz_subject->seo_description = $params['description'];
        $tbz_subject->status = $params['status'];
        $tbz_subject->sort = $params['sort'];
        if ($tbz_subject->save(false)) {
            return $tbz_subject;
        } else {
            return false;
        }
    }

    public function TbzSubjectUpdate($params)
    {
        $tbz_subject = TbzSubject::findOne($params['id']);
        if (!$tbz_subject) {
            return false;
        }
        $tbz_subject->title = $params['title'];
        $tbz_subject->description = $params['description'];
        $tbz_subject->banner = $params['banner'];
        $tbz_subject->thumbnail = $params['thumbnail'];
        $tbz_subject->seo_title = $params['seo_title'];
        $tbz_subject->seo_keyword = $params['seo_keyword'];
        $tbz_subject->seo_description = $params['description'];
        $tbz_subject->status = $params['status'];
        $tbz_subject->sort = $params['sort'];
        if ($tbz_subject->save(false)) {
            return $tbz_subject;
        } else {
            return false;
        }
    }
}