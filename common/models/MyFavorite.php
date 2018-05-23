<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;

/**
 * This is the model class for table "{{%my_favorite}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="MyFavorite"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $template_id @SWG\Property(property="templateId", type="integer", description="")
 * @property int $user_id @SWG\Property(property="userId", type="integer", description="")
 * @property int $team_id @SWG\Property(property="teamId", type="integer", description="")
 * @property int $created_at @SWG\Property(property="createdAt", type="integer", description="")
 * @property int $updated_at @SWG\Property(property="updatedAt", type="integer", description="")
 */
class MyFavorite extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%my_favorite}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'user_id', 'team_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'user_id' => 'User ID',
            'team_id' => 'Team ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * 关联TemplateOfficial
     */
    public function getTemplateOfficials()
    {
        return $this->hasMany(TemplateOfficial::class, ['template_id' => 'template_id'])
            ->where(['status' => TemplateOfficial::STATUS_ONLINE]);
    }
}
