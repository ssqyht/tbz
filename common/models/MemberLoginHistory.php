<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%member_login_history}}".
 *
 * @property int $history_id
 * @property int $user_id 用户id
 * @property int $method 登录方式
 * @property string $ip 登录ip
 * @property string $http_user_agent
 * @property string $http_referer 登录来源
 * @property string $login_url 登录页面url
 * @property int $created_at 创建时间
 */
class MemberLoginHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_login_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'method', 'ip'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['method'], 'string', 'max' => 1],
            [['ip'], 'string', 'max' => 64],
            [['http_user_agent', 'http_referer', 'login_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'history_id' => 'History ID',
            'user_id' => 'User ID',
            'method' => 'Method',
            'ip' => 'Ip',
            'http_user_agent' => 'Http User Agent',
            'http_referer' => 'Http Referer',
            'login_url' => 'Login Url',
            'created_at' => 'Created At',
        ];
    }
}
