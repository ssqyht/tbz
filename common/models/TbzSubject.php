<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
use yii\helpers\Url;

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
 * @property int $status 是否上线 @SWG\Property(property="status", type="integer", description=" 是否上线")
 * @property int $sort 排序逆序 @SWG\Property(property="sort", type="integer", description=" 排序逆序")
 * @property int $created_time 创建日期 @SWG\Property(property="createdTime", type="integer", description=" 创建日期")
 * @property int $updated_time 修改时间 @SWG\Property(property="updatedTime", type="integer", description=" 修改时间")
 */
class TbzSubject extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;
    /** @var int 模板专题上线 */
    const STATUS_ONLINE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbz_subject}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 150],
            [['description', 'seo_keyword', 'seo_description'], 'string', 'max' => 255],
            [['thumbnail', 'seo_title'], 'string', 'max' => 100],
            [['banner'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '唯一标识',
            'title' => '模板标题',
            'description' => '描述',
            'thumbnail' => '缩略图路径',
            'banner' => '专题内页banner图路径',
            'seo_title' => 'Seo标题',
            'seo_keyword' => 'Seo关键词',
            'seo_description' => 'Seo描述',
            'status' => '模板状态',
            'sort' => '热度',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function frontendFields()
    {
        return ['id', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function sortHot()
    {
        {
            return TbzSubject::find()->orderBy(['sort' => SORT_DESC]);
        }
    }

    /**
     * 上线分类
     * @return \yii\db\ActiveQuery
     * @author thanatos <thanatos915@163.com>
     */
    public static function online()
    {
        return static::sortHot()->andWhere(['status' => static::STATUS_ONLINE]);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * 更新缓存
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 更新缓存
        if ($changedAttributes) {
            Yii::$app->dataCache->updateCache(static::class);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findById($id)
    {
        if (Yii::$app->request->isFrontend()) {
            return static::find()->where(['status' => static::STATUS_ONLINE, 'id' => $id])->one();
        } else {
            return static::find()->where(['id' => $id])->one();
        }
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        $data['thumbnail'] = function () {
            return Url::to('@oss') . DIRECTORY_SEPARATOR . 'uploads' . $this->thumbnail;
        };
        $data['banner'] = function () {
            return Url::to('@oss') . DIRECTORY_SEPARATOR . 'uploads' . $this->banner;
        };
        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplates()
    {
        return $this->hasMany(TemplateOfficial::class, ['template_id' => 'template_id'])
            ->where(['status' => TemplateOfficial::STATUS_ONLINE])
            ->orderBy(['created_at'=>SORT_DESC])
            ->with(['myFavorite','classifyName'])
            ->viaTable(TemplateTopic::tableName(),['topic_id'=>'id']);
    }
}
