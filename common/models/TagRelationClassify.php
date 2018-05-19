<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%tag_relation_classify}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="TagRelationClassify"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $tag_id tag表id @SWG\Property(property="tagId", type="integer", description=" tag表id")
 * @property int $classify_id classify表id @SWG\Property(property="classifyId", type="integer", description=" classify表id")
 * @property int $created_time 创建日期 @SWG\Property(property="createdTime", type="integer", description=" 创建日期")
 * @property int $updated_time 修改时间 @SWG\Property(property="updatedTime", type="integer", description=" 修改时间")
 */
class TagRelationClassify extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag_relation_classify}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'classify_id', 'created_time', 'updated_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'tag表id',
            'classify_id' => 'classify表id',
            'created_time' => '创建日期',
            'updated_time' => '修改时间',
        ];
    }
}
