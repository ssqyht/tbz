<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
/**
 * This is the model class for table "{{%team_member}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="TeamMember"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $user_id 成员id @SWG\Property(property="userId", type="integer", description=" 成员id")
 * @property string $team_name 团队名称 @SWG\Property(property="teamName", type="string", description=" 团队名称")
 * @property int $team_id 团队id @SWG\Property(property="teamId", type="integer", description=" 团队id")
 * @property int $status 状态 @SWG\Property(property="status", type="integer", description=" 状态")
 * @property int $role 角色 @SWG\Property(property="role", type="integer", description=" 角色")
 * @property int $invite_id 邀请表的id @SWG\Property(property="inviteId", type="integer", description=" 邀请表的id")
 * @property int $authority 权限 @SWG\Property(property="authority", type="integer", description=" 权限")
 * @property int $created_at 创建日期 @SWG\Property(property="createdAt", type="integer", description=" 创建日期")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class TeamMember extends \yii\db\ActiveRecord
{
    use ModelFieldsTrait;
    use TimestampTrait;
    /** @var int 团队正常状态值 */
    const NORMAL_STATUS = 10;
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;
    /** @var int 拉黑状态 */
    const DELETE_STATUS = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'team_id', 'status', 'role', 'invite_id', 'authority', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'team_id' => 'Team ID',
            'status' => 'Status',
            'role' => 'Role',
            'invite_id' => 'Invite ID',
            'authority' => 'Authority',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * 正常团队成员
     * @return \yii\db\ActiveQuery
     */
    public static function online()
    {
        return TeamMember::find()->where(['status' => static::NORMAL_STATUS]);
    }
    /**
     * @return array
     */
    public function frontendFields()
    {
        return [
            'id', 'team_id', 'user_id', 'role'
        ];
    }
    public function extraFields()
    {
        if ($this->isRelationPopulated('memberMark')) {
            $data['user_mark'] = function () {
                return $this->memberMark->headimg_url;
            };
        }
        return $data;
    }
    /**
     * 关联团队成员表
     * @return \yii\db\ActiveQuery
     */
    public function getMemberMark()
    {
        return $this->hasOne(Member::class, ['id' => 'user_id']);
    }
}
