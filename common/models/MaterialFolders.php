<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
/**
 * This is the model class for table "{{%material_folders}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="MaterialFolders"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $name 文件夹名称 @SWG\Property(property="name", type="string", description=" 文件夹名称")
 * @property string $color 文件夹颜色 @SWG\Property(property="color", type="string", description=" 文件夹颜色")
 * @property int $status 文件夹状态 @SWG\Property(property="status", type="integer", description=" 文件夹状态")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property int $team_id 团队id @SWG\Property(property="teamId", type="integer", description=" 团队id")
 * @property int $created_at 创建日期 @SWG\Property(property="createdAt", type="integer", description=" 创建日期")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class MaterialFolders extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;
    /** @var int 正常状态 */
    const STATUS_ONLINE = 10;

    static $frontendFields = ['id','color', 'name','user_id', 'team_id'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material_folders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'user_id', 'team_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'color' => 'Color',
            'status' => 'Status',
            'user_id' => 'User ID',
            'team_id' => 'Team ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function sortTime(){
        {
            return MaterialFolders::find()->orderBy(['created_at' => SORT_DESC]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function online()
    {
        return static::sortTime()->Where(['status' => static::STATUS_ONLINE]);
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
