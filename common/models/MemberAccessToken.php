<?php

namespace common\models;

use common\components\traits\TimestampTrait;
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
    use TimestampTrait;

    /** @var int web 网页端登录 */
    const TOKEN_TYPE_WEB = 1;
    /** @var int 移动端 */
    const TOKEN_TYPE_MOBILE = 2;
    /** @var int 小程序端 */
    const TOKEN_TYPE_MINI_PROGRAM = 3;
    /** @var int 设备号最大值 */
    const MAX_TOKEN_TYPE = self::TOKEN_TYPE_MINI_PROGRAM;

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
            ['token_type', 'default', 'value' => 0],
            [['user_id', 'expired_at'], 'integer'],
            [['access_token', 'token_unique', 'user_id'], 'required'],
            [['access_token', 'token_unique'], 'string', 'max' => 32],
            [['token_type'], 'integer', 'max' => static::MAX_TOKEN_TYPE],
            ['expired_at', 'default', 'value' => time() + Member::LOGIN_DURATION]
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

    /**
     * 生成access_token
     * @param $tokenType
     * @return bool|MemberAccessToken
     */
    public static function createAccessToken($tokenType = 0)
    {
        $accessToken = '';
        try {
            $accessToken = Yii::$app->security->generateRandomString();
        } catch (\Exception $exception) {}

        $tokenUnique = md5(Yii::$app->request->userAgent);

        $model = new static();
        $model->load([
            'user_id' => Yii::$app->user->id,
            'token_type' => $tokenType,
            'access_token' => $accessToken,
            'token_unique' => $tokenUnique,
        ], '');
        if (!$model->validate()) {
            return false;
        }
        // 删除当前设备的登录记录
        try {
            static::getDb()->createCommand()->delete(static::tableName(), [
                'user_id' => $model->user_id,
                'token_type' => $tokenType,
                'token_unique' => $tokenUnique,
            ])->execute();
        } catch (\Exception $e) {
            var_dump($e);exit;
        }

        return $model->save() ? $model : false;
    }


}
