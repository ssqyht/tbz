<?php

namespace common\models;

use common\components\traits\TimestampTrait;
use common\components\validators\MobileValidator;
use Yii;
use yii\web\IdentityInterface;

/**
 * 用户类
 * @SWG\Definition(type="object", @SWG\Xml(name="Member"))
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $mobile 用户手机号
 * @property int $sex 姓别
 * @property int $headimg_id 头像id
 * @property string $headimg_url 头像url
 * @property int $coin 图币
 * @property int $last_login_time 最后登录时间
 * @property string $password_hash 密码hash
 * @property string $salt 旧salt
 * @property string $status 用户状态
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 * @property MemberAccessToken $accessToken
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{

    use TimestampTrait;

    const LOGIN_DURATION = 3600*24*15;

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
            [['headimg_url'], 'string', 'max' => 255],
            ['headimg_url', 'default', 'value' => ''],
            [['mobile'], 'string', 'max' => 11],
            ['mobile', MobileValidator::class],
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
            'headimg_url' => 'Headimg Url',
            'coin' => 'Coin',
            'last_login_time' => 'Last Login Time',
            'status' => 'Status',
            'password_hash' => 'Password Hash',
            'salt' => 'Salt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id', 'username','mobile', 'sex', 'headimg_url', 'coin'
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {}

    public function validateAuthKey($authKey)
    {}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessToken()
    {
        return $this->hasOne(MemberAccessToken::class, ['user_id' => 'id']);
    }

    /**
     * 返回登录保持时间
     * @return float|int
     */
    public function getDuration()
    {
        return static::LOGIN_DURATION;
    }

}
