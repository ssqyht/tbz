<?php

namespace common\models;

use common\components\traits\ModelErrorTrait;
use function foo\func;
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
 * @property Classify[] $classifies
 */
class Category extends \yii\db\ActiveRecord
{
    use ModelErrorTrait;
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

    public function fields()
    {
        $data = [
            'name', 'product',
            'className' => function(){
                return $this->class_name;
            }
        ];
        if ($this->isRelationPopulated('classifies')) {
            $data['classifies'] = function () {
                return $this->classifies;
            };
        }

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author thanatos <thanatos915@163.com>
     * @internal
     */
    public function getClassifies()
    {
        return $this->hasMany(Classify::class, ['category' => 'id']);
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

    /**
     * 返回默认Query
     * @return \yii\db\ActiveQuery
     * @author thanatos <thanatos915@163.com>
     */
    public static function active()
    {
        return Category::find()->orderBy(['sort' => SORT_ASC]);
    }


}
