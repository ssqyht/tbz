<?php

namespace common\models;

use common\components\traits\TimestampTrait;
use common\components\validators\MobileValidator;
use Firebase\JWT\JWT;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\web\IdentityInterface;

/**
 * 用户类
 * @SWG\Definition(type="object", @SWG\Xml(name="Member"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property string $username 用户名 @SWG\Property(property="username", type="string", description=" 用户名")
 * @property string $mobile 用户手机号 @SWG\Property(property="mobile", type="string", description=" 用户手机号")
 * @property int $sex 姓别 @SWG\Property(property="sex", type="integer", description=" 姓别")
 * @property int $headimg_id 头像ID
 * @property string $headimg_url 头像url @SWG\Property(property="headimgUrl", type="string", description=" 头像url")
 * @property int $coin 图币 @SWG\Property(property="coin", type="integer", description=" 图币")
 * @property int $last_login_time 最后登录时间 @SWG\Property(property="lastLoginTime", type="integer", description=" 最后登录时间")
 * @property string $password_hash 密码hash
 * @property string $salt 旧salt
 * @property string $password 旧密码
 * @property int $status 用户状态
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
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

    // token 过期时间 5小时
    const EXPIRED_TIME = 3600*5;
    // token 刷新时间 15天
    const REFRESH_TIME = 3600*24*15;

    /**
     * 用于接口返回
     * @SWG\Property(property="accessToken", type="string", description="")
     * @var string
     */
    public $access_token;

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
            [['password'], 'string', 'max' => 32],
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
            'password' => 'password',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id', 'username','mobile', 'sex', 'headimg_url', 'coin',
            'accessToken' => function($model) {
                return $model->access_token;
            }
        ];
    }

    /**
     * 通过mobile查找
     * @param $mobile
     * @return Member|null
     * @author thanatos <thanatos915@163.com>
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }


    /**
     * 生成JWT TOKEN
     * 如果需要更新，需手动清除缓存
     * @return string
     * @author thanatos <thanatos915@163.com>
     */
    public function generateJwtToken()
    {
        $time = time();
        // 定义payload属性
        $token = [
            'iss' => 'https://www.tubangzhu.com',
            'aud' => 'tubangzhu_web',
            'sub' => $this->id,
            'exp' => $time + static::EXPIRED_TIME,
            'iat' => $time,
            'ref' => $time + static::REFRESH_TIME,
            'data' => [
                'name' => $this->username,
                'headimg_url' => $this->headimg_url,
            ]
        ];

        // 通过缓存取得密钥
        $cache = Yii::$app->cache;
        $cacheKey = [
            OauthPublicKeys::class,
            'JWT_clients_cache',
        ];
        if (!$jwt = $cache->get($cacheKey)) {
            /** @var OauthPublicKeys $jwt */
            $jwt = OauthPublicKeys::find()->where(['client_id' => 'tubangzhu_web'])->one();
            $cache->set($cacheKey, $jwt);
        }

        return JWT::encode($token, $jwt->primaryKey, $jwt->encryption_algorithm);
    }

    /**
     * 验证用户密码
     * @param string $password 用户密码
     * @return bool
     * @author thanatos <thanatos915@163.com>
     */
    public function validatePassword($password)
    {
        // 验证老的密码体系
        if ($this->salt) {
            if ($this->password == md5(md5($password), $this->salt)) {
                // 通过后重置新的密码格式
                try {
                    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
                }catch (\Throwable $exception) {
                    return false;
                }
                return $this->save() ?: false;
            }
        }
        // 验证新密码格式
        if ($this->password_hash) {
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        }
        return false;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {}

    public function validateAuthKey($authKey)
    {}

}
