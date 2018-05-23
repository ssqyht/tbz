<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%material_classify}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="MaterialClassify"))
 *
 * @property int $cid @SWG\Property(property="cid", type="integer", description="")
 * @property string $name 分类名称 @SWG\Property(property="name", type="string", description=" 分类名称")
 * @property int $status 素材分类状态 @SWG\Property(property="status", type="integer", description=" 素材分类状态")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class MaterialClassify extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material_classify}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cid' => 'Cid',
            'name' => '分类名称',
            'status' => '素材分类状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
