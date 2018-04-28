<?php

namespace common\models;

use common\components\traits\TimestampTrait;
use Yii;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $mobile 用户手机号
 * @property int $sex 姓别
 * @property int $headimg_id 头像
 * @property int $coin 图币
 * @property int $last_login_time 最后登录时间
 * @property string $password_hash 密码hash
 * @property string $salt 旧salt
 * @property string $status 用户状态
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Member extends \yii\db\ActiveRecord
{
    use TimestampTrait;

    /** @var int 男 */
    const SEX_MALE = 1;
    /** @var int 女 */
    const SEX_WOMAN = 2;
    /** @var int 未知 */
    const SEX_UNKNOWN = 0;
    /** @var int max sex */
    const SEX_MAX = self::SEX_WOMAN;

    /** @var int 用户正常状态 */
    const STATUS_NORMAL = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['headimg_id', 'coin', 'last_login_time'], 'integer'],
            [['username'], 'string', 'max' => 30],
            [['mobile'], 'string', 'max' => 11],
            [['sex', 'status'], 'integer', 'max' => 255],
            ['status', 'default', 'value' => 10],
            [['password_hash'], 'string', 'max' => 60],
            [['salt'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'mobile' => 'Mobile',
            'sex' => 'Sex',
            'headimg_id' => 'Headimg ID',
            'coin' => 'Coin',
            'last_login_time' => 'Last Login Time',
            'status' => 'Status',
            'password_hash' => 'Password Hash',
            'salt' => 'Salt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
