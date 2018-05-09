<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%category}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Category"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $name 品类名称 @SWG\Property(property="name", type="string", description=" 品类名称")
 * @property string $class_name 品类class名 @SWG\Property(property="className", type="string", description=" 品类class名")
 * @property string $product 品类唯一标识 @SWG\Property(property="product", type="string", description=" 品类唯一标识")
 * @property int $sort 品类排序 @SWG\Property(property="sort", type="integer", description=" 品类排序")
 */
class Category extends \yii\db\ActiveRecord
{

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
}
