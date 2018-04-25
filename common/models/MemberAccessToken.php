<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%member_access_token}}".
 *
 * @property int $token_id
 * @property int $user_id 用户ID
 * @property string $access_token access_token
 * @property int $token_type 登录设备号
 * @property int $expired_at 过期时间
 * @property string $token_unique 设备唯一串
 * @property int $created_at 创建时间
 */
class MemberAccessToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_access_token}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'expired_at'], 'integer'],
            [['access_token', 'token_unique', 'created_at'], 'required'],
            [['access_token', 'token_unique'], 'string', 'max' => 32],
            [['token_type'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'token_id' => 'Token ID',
            'user_id' => 'User ID',
            'access_token' => 'Access Token',
            'token_type' => 'Token Type',
            'expired_at' => 'Expired At',
            'token_unique' => 'Token Unique',
            'created_at' => 'Created At',
        ];
    }
}
