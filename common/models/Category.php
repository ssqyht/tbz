<?php

namespace common\models;

use common\components\traits\ModelTrait;
use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%category}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Category"))
 *
 * @property int $id
 * @property string $name 品类名称 @SWG\Property(property="name", type="string", description=" 品类名称")
 * @property string $class_name 品类class名 @SWG\Property(property="className", type="string", description=" 品类class名")
 * @property string $product 品类唯一标识 @SWG\Property(property="product", type="string", description=" 品类唯一标识")
 * @property int $sort 品类排序 @SWG\Property(property="sort", type="integer", description=" 品类排序")
 */
class Category extends \yii\db\ActiveRecord
{
    use ModelTrait;
    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class_name', 'product'], 'required'],
            [['sort'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['class_name'], 'string', 'max' => 15],
            [['product'], 'string', 'max' => 30],
            ['product', 'unique', 'message' => 'product 已存在'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '品类名称',
            'class_name' => '品类class名',
            'product' => '品类唯一标识',
            'sort' => '品类排序',
        ];
    }

    /**
     * 根据ID查询
     * @param integer $id
     * @return Category|null
     * @author thanatos <thanatos915@163.com>
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

}
