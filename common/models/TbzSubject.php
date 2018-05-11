<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%tbz_subject}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="TbzSubject"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $title 文章标题 @SWG\Property(property="title", type="string", description=" 文章标题")
 * @property string $description 专题描述 @SWG\Property(property="description", type="string", description=" 专题描述")
 * @property string $thumbnail 缩略图 @SWG\Property(property="thumbnail", type="string", description=" 缩略图")
 * @property string $banner 专题内页banner图 @SWG\Property(property="banner", type="string", description=" 专题内页banner图")
 * @property string $seo_title SEO标题 @SWG\Property(property="seoTitle", type="string", description=" SEO标题")
 * @property string $seo_keyword SEO关键词 @SWG\Property(property="seoKeyword", type="string", description=" SEO关键词")
 * @property string $seo_description SEO描述 @SWG\Property(property="seoDescription", type="string", description=" SEO描述")
 * @property int $status SEO描述 @SWG\Property(property="status", type="integer", description=" SEO描述")
 * @property int $sort 排序逆序 @SWG\Property(property="sort", type="integer", description=" 排序逆序")
 * @property int $created_time 创建日期 @SWG\Property(property="createdTime", type="integer", description=" 创建日期")
 * @property int $updated_time 修改时间 @SWG\Property(property="updatedTime", type="integer", description=" 修改时间")
 */
class TbzSubject extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{tbz_subject}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'created_time', 'updated_time'], 'integer'],
            [['title'], 'string', 'max' => 150],
            [['description', 'seo_keyword', 'seo_description'], 'string', 'max' => 255],
            [['thumbnail', 'seo_title'], 'string', 'max' => 100],
            [['banner'], 'string', 'max' => 60],
            [['status'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'thumbnail' => 'Thumbnail',
            'banner' => 'Banner',
            'seo_title' => 'Seo Title',
            'seo_keyword' => 'Seo Keyword',
            'seo_description' => 'Seo Description',
            'status' => 'Status',
            'sort' => 'Sort',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
