<?php

namespace common\models;

use common\traits\TimestampTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%center_user}}".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $mobile 手机号
 * @property int $sex 姓别
 * @property int $status 用户状态
 * @property string $password_hash 密码hash
 * @property string $salt 旧salt
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class CenterUser extends ActiveRecord
{
    use TimestampTrait;

    /** @var int 男 */
    const SEX_MALE = 1;
    /** @var int 女 */
    const SEX_WOMAN = 2;
    /** @var int 未知 */
    const SEX_UNKNOWN = 0;
    /** @var int max sex */
    const MAX_SEX = 2;

    /** @var int 用户正常状态 */
    const STATUS_NORMAL = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%center_user}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 30],
            [['mobile'], 'string', 'max' => 11],
            [['sex', 'status'], 'integer', 'max' => 255],
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
            'status' => 'Status',
            'password_hash' => 'Password Hash',
            'salt' => 'Salt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
