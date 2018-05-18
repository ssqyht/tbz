<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
/**
 * This is the model class for table "{{%tbz_letter}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="TbzLetter"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $title 文章标题 @SWG\Property(property="title", type="string", description=" 文章标题")
 * @property string $subtitle 副标题 @SWG\Property(property="subtitle", type="string", description=" 副标题")
 * @property string $description 消息内容 @SWG\Property(property="description", type="string", description=" 消息内容")
 * @property int $type 消息类型(1为公共通知，2为活动通知，3为个人消息 @SWG\Property(property="type", type="integer", description=" 消息类型(1为公共通知，2为活动通知，3为个人消息")
 * @property int $status 信息状态(1为待发布，2为直接发布) @SWG\Property(property="status", type="integer", description=" 信息状态(1为待发布，2为直接发布)")
 * @property int $sort 排序逆序 @SWG\Property(property="sort", type="integer", description=" 排序逆序")
 * @property int $user_id 当消息为个人消息时，接收消息的用户 @SWG\Property(property="userId", type="integer", description=" 当消息为个人消息时，接收消息的用户")
 * @property int $created_time 创建日期 @SWG\Property(property="createdTime", type="integer", description=" 创建日期")
 * @property int $updated_time 修改时间 @SWG\Property(property="updatedTime", type="integer", description=" 修改时间")
 */
class TbzLetter extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;
    /** @var int 消息状态 */
    const STATUS_ONLINE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tbz_letter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'sort', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['subtitle'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 500],
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
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'type' => 'Type',
            'status' => 'Status',
            'sort' => 'Sort',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function frontendFields()
    {
        return ['title', 'subtitle','description', 'type','user_id'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function sortTime(){
        {
            return TbzLetter::find()->orderBy(['updated_at' => SORT_DESC]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function online()
    {
        return static::sortTime()->andWhere(['status' => static::STATUS_ONLINE]);
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
}
