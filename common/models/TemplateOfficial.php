<?php

namespace common\models;

use common\components\traits\ModelFieldsTrait;
use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelErrorTrait;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%template_official}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="TemplateOfficial"))
 *
 * @property int $template_id @SWG\Property(property="templateId", type="integer", description="")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property int $cooperation_id 商户id @SWG\Property(property="cooperationId", type="integer", description=" 商户id")
 * @property string $title 模板标题 @SWG\Property(property="title", type="string", description=" 模板标题")
 * @property string $product 模板分类 @SWG\Property(property="product", type="string", description=" 模板分类")
 * @property string $thumbnail_url 模板缩略图 @SWG\Property(property="thumbnailUrl", type="string", description=" 模板缩略图")
 * @property int $thumbnail_id 模板id @SWG\Property(property="thumbnailId", type="integer", description=" 模板id")
 * @property int $status 状态 @SWG\Property(property="status", type="integer", description=" 状态")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 * @property int $price 模板价格 @SWG\Property(property="price", type="integer", description=" 模板价格")
 * @property int $amount_edit 编辑量 @SWG\Property(property="amountEdit", type="integer", description=" 编辑量")
 * @property int $virtual_edit 虚拟编辑量 @SWG\Property(property="virtualEdit", type="integer", description=" 虚拟编辑量")
 * @property int $amount_view 浏览量 @SWG\Property(property="amountView", type="integer", description=" 浏览量")
 * @property int $virtual_view 虚拟浏览量 @SWG\Property(property="virtualView", type="integer", description=" 虚拟浏览量")
 * @property int $amount_favorite 收藏量 @SWG\Property(property="amountFavorite", type="integer", description=" 收藏量")
 * @property int $virtual_favorite 虚拟收藏量 @SWG\Property(property="virtualFavorite", type="integer", description=" 虚拟收藏量")
 * @property int $amount_buy 购买量 @SWG\Property(property="amountBuy", type="integer", description=" 购买量")
 * @property int $sort 排序 @SWG\Property(property="sort", type="integer", description=" 排序")
 * @property string $content 模板数据 @SWG\Property(property="content", type="string", description=" 模板数据")
 */
class TemplateOfficial extends \yii\db\ActiveRecord
{
    use ModelErrorTrait;
    use TimestampTrait;
    use ModelFieldsTrait;
    const STATUS_ONLINE = 20;

    static $frontendFields = [
        'template_id', 'user_id', 'title','product','thumbnail_id','created_at','updated_at','price', 'virtual_edit', 'virtual_view', 'virtual_favorite'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%template_official}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'cooperation_id', 'created_at', 'updated_at', 'content'], 'required'],
            [['user_id', 'cooperation_id', 'thumbnail_id', 'created_at', 'updated_at', 'price', 'amount_edit', 'virtual_edit', 'amount_view', 'virtual_view', 'amount_favorite', 'virtual_favorite', 'amount_buy', 'sort'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 50],
            [['product'], 'string', 'max' => 30],
            [['thumbnail_url'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 1],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template_id' => 'Template ID',
            'user_id' => '用户id',
            'cooperation_id' => '商户id',
            'title' => '模板标题',
            'product' => '模板分类',
            'thumbnail_url' => '模板缩略图',
            'thumbnail_id' => '模板id',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'price' => '模板价格',
            'amount_edit' => '编辑量',
            'virtual_edit' => '虚拟编辑量',
            'amount_view' => '浏览量',
            'virtual_view' => '虚拟浏览量',
            'amount_favorite' => '收藏量',
            'virtual_favorite' => '虚拟收藏量',
            'amount_buy' => '购买量',
            'sort' => '排序',
            'content' => '模板数据',
        ];
    }

    public function extraFields()
    {
        $data = [
          'thumbnailUrl' => function(){
            return Url::to('@oss') . DIRECTORY_SEPARATOR . $this->thumbnail_url;
          }
        ];
        return $data;
    }

    /**
     * 保存成功后更新缓存
     * @param bool $insert
     * @param array $changedAttributes
     * @author thanatos <thanatos915@163.com>
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 更新缓存
        if ($changedAttributes) {
            Yii::$app->dataCache->updateCache(static::class);
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     * 按热度排序
     */
    public static function sort()
    {
        return static::find()->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * 查找线上模板
     */
    public static function online()
    {
        return static::sort()->andWhere(['status' => static::STATUS_ONLINE]);
    }
}
