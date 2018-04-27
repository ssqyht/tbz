<?php

namespace common\models;

use common\traits\TimestampTrait;
use Yii;

/**
 * This is the model class for table "{{%center_user_oauth}}".
 *
 * @property int $id
 * @property int $center_id 用户ID
 * @property int $oauth_name 第三方名称
 * @property string $oauth_key 第三方key值
 * @property int $created_at 创建时间
 */
class CenterUserOauth extends \yii\db\ActiveRecord
{
    use TimestampTrait;

    /** @var int 微信 */
    const OAUTH_WECHAT = 1;
    /** @var int QQ */
    const OAUTH_QQ = 2;
    /** @var int max oauth_name */
    const MAX_OAUTH_NAME = self::OAUTH_QQ;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%center_user_oauth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['center_id', 'oauth_name', 'oauth_key'], 'required'],
            [['center_id', 'created_at'], 'integer'],
            [['oauth_name'], 'integer', 'max' => static::MAX_OAUTH_NAME, 'min' => 1],
            [['oauth_key'], 'string', 'max' => 50],
            ['oauth_key', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'center_id' => 'Center ID',
            'oauth_name' => 'Oauth Name',
            'oauth_key' => 'Oauth Key',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 根据第三方名和唯一值查询
     * @param string $oauthName
     * @param string $oauthKey
     * @return null|static
     */
    public static function findByNameAndKey($oauthName, $oauthKey)
    {
        return static::findOne(['oauth_name' => $oauthName, 'oauth_key' => $oauthKey]);
    }

}
