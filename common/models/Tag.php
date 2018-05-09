<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%tag}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Tag"))
 *
 * @property int $tag_id @SWG\Property(property="tagId", type="integer", description="")
 * @property string $name Tag名称 @SWG\Property(property="name", type="string", description=" Tag名称")
 * @property int $type tag种类 @SWG\Property(property="type", type="integer", description=" tag种类")
 * @property int $sort 排序名称 @SWG\Property(property="sort", type="integer", description=" 排序名称")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class Tag extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'updated_at'], 'required'],
            [['sort', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['type'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'name' => 'Tag名称',
            'type' => 'tag种类',
            'sort' => '排序名称',
            'updated_at' => '修改时间',
        ];
    }
}
