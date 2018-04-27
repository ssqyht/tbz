<?php
/**
 * @user: thanatos
 */

namespace common\models\forms;


use common\components\traits\funcTraits;
use common\extension\Code;
use common\models\FileCommon;
use common\models\Member;
use Yii;
use common\models\CenterUser;
use common\models\CenterUserOauth;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;

class RegisterForm extends Model
{
    public $username;
    public $sex;
    public $headimgul;
    public $oauth_key;
    public $oauth_name;

    const SCENARIO_OAUTH = 'oauth';

    public function rules()
    {
        return [
            [['username', 'sex', 'oauth_name', 'oauth_key'], 'required'],
            [['headimgurl'], 'string'],
            [['username'], 'string', 'max' => 30],
            [['sex'], 'integer', 'max' => CenterUser::SEX_MAX, 'min' => 0],
            [['oauth_name'], 'integer', 'max' => CenterUserOauth::MAX_OAUTH_NAME, 'min' => 1],
            ['oauth_key', 'string', 'max' => 50],
            ['oauth_key', 'validateOauthKey'],
        ];
    }

    public function validateOauthKey($attribute, $params)
    {
        if (CenterUserOauth::findByNameAndKey($this->oauth_name, $this->oauth_key)) {
            $this->addError($attribute, Code::USER_EXIST);
        }
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = [
            static::SCENARIO_OAUTH => ['username', 'sex', 'oauth_name', 'oauth_key'],
        ];
        return ArrayHelper::merge(parent::scenarios(), $scenarios);
    }

    /**
     * 用户注册
     * @return bool|Member
     * @throws Exception
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 创建center记录
            $centerUser = new CenterUser();
            $centerUser->username = $this->username;
            $centerUser->sex = $this->sex;
            $centerUser->status = CenterUser::STATUS_NORMAL;
            if (!$centerUser->save()) {
                throw new Exception('center user save failed');
            }

            // 创建user_oath记录
            $centerUserOauth = new CenterUserOauth();
            $centerUserOauth->center_id = $centerUser->id;
            $centerUserOauth->oauth_name = $this->oauth_name;
            $centerUserOauth->oauth_key = $this->oauth_key;
            if (!$centerUserOauth->save()) {
                throw new Exception('center user oauth failed');
            }

            // 创建member记录
            $member = new Member();
            $member->sex = $centerUser->sex;
            $member->center_id= $centerUser->id;
            // 生成头像
            if ($this->headimgurl && $result = FileUpload::upload($this->headimgul)) {
                $member->headimg_id = $result->file_id ?? 0;
            }
            if (!$member->save()) {
                throw new Exception('member save failed');
            }
            $transaction->commit();
            return $member;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            return false;
        }

    }

}