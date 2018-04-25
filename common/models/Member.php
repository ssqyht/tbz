<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property int $id
 * @property int $center_id 用户中心ID
 * @property string $mobile 用户手机号
 * @property int $sex 姓别
 * @property int $headimg_id 头像
 * @property int $coin 图币
 * @property int $last_login_time 最后登录时间
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Member extends \yii\db\ActiveRecord
{
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
            [['center_id', 'headimg_id', 'coin', 'last_login_time'], 'integer'],
            [['mobile', 'created_at', 'updated_at'], 'required'],
            [['mobile'], 'string', 'max' => 11],
            [['sex'], 'string', 'max' => 1],
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
            'mobile' => 'Mobile',
            'sex' => 'Sex',
            'headimg_id' => 'Headimg ID',
            'coin' => 'Coin',
            'last_login_time' => 'Last Login Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
