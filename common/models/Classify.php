<?php

namespace common\models;

use Yii;
use common\components\traits\ModelFieldsTrait;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%classify}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Classify"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $product 模板分类标识 @SWG\Property(property="product", type="string", description=" 模板分类标识")
 * @property string $parent_product 父分类 @SWG\Property(property="parentProduct", type="string", description=" 父分类")
 * @property string $category 所属品类标识 @SWG\Property(property="category", type="string", description=" 所属品类标识")
 * @property string $name 分类名称 @SWG\Property(property="name", type="string", description=" 分类名称")
 * @property string $parent_name 父分类名称 @SWG\Property(property="parentName", type="string", description=" 父分类名称")
 * @property int $default_price 默认价格 @SWG\Property(property="defaultPrice", type="integer", description=" 默认价格")
 * @property int $is_hot 是否是热门 @SWG\Property(property="isHot", type="integer", description=" 是否是热门")
 * @property int $is_new 是否是新上 @SWG\Property(property="isNew", type="integer", description=" 是否是新上")
 * @property string $default_edit 模板默认配置 @SWG\Property(property="defaultEdit", type="string", description=" 模板默认配置")
 * @property string $order_link 下单连接 @SWG\Property(property="orderLink", type="string", description=" 下单连接")
 * @property string $thumbnail 缩略图 @SWG\Property(property="thumbnail", type="string", description=" 缩略图")
 * @property int $thumbnail_id 缩略图file_id @SWG\Property(property="thumbnailId", type="integer", description=" 缩略图file_id")
 * @property int $sort 排序值 @SWG\Property(property="sort", type="integer", description=" 排序值")
 * @property int $is_open 是否对外开放
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Classify extends \yii\db\ActiveRecord
{
    use TimestampTrait;
    use ModelFieldsTrait;

    /** @var int 分类下线 */
    const STATUS_OFFLINE = 10;
    /** @var int 分类下线 */
    const STATUS_ONLINE = 20;

    /** @var array 公共返回数据 */
    static $frontendFields = [
        'product', 'name', 'parent_name', 'is_hot', 'is_new', 'order_link', 'thumbnail'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%classify}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product', 'category', 'name', 'parent_name', 'default_edit', 'created_at', 'updated_at'], 'required'],
            [['default_price', 'thumbnail_id', 'sort', 'status','created_at', 'updated_at'], 'integer'],
            [['default_edit'], 'string'],
            [['product', 'parent_product', 'category'], 'string', 'max' => 30],
            [['name', 'parent_name'], 'string', 'max' => 10],
            [['is_hot', 'is_new', 'is_open'], 'string', 'max' => 1],
            [['order_link', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product' => '模板分类标识',
            'parent_product' => '父分类',
            'category' => '所属品类标识',
            'name' => '分类名称',
            'parent_name' => '父分类名称',
            'default_price' => '默认价格',
            'is_hot' => '是否是热门',
            'is_new' => '是否是新上',
            'default_edit' => '模板默认配置',
            'order_link' => '下单连接',
            'thumbnail' => '缩略图',
            'thumbnail_id' => '缩略图file_id',
            'sort' => '排序值',
            'is_open' => '是否对外开放',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }


}
