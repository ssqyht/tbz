<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

use common\models\Member;
use common\models\MemberAccessToken;
use Yii;
use common\components\validators\MobileValidator;
use common\extension\Code;
use common\models\MemberOauth;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * 统一登录类
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class LoginForm extends Model
{
    /** @var string 第三方授权登录 */
    const SCENARIO_OAUTH = 'oauth';
    /** @var string 手机号登录 */
    const SCENARIO_MOBILE = 'mobile';
    /** @var string 系统自动登录，不生成access_token */
    const SCENARIO_SYSTEM = 'system';

    public $oauth_name;
    public $oauth_key;
    public $mobile;
    public $password;
    public $token_type;

    public function rules()
    {
        return [
            ['token_type', 'default', 'value' => 0],
            [['oauth_name', 'oauth_key', 'mobile', 'password'], 'required'],
            [['oauth_name'], 'integer', 'max' => MemberOauth::MAX_OAUTH_NAME, 'min' => 1],
            [['oauth_key'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 11],
            ['mobile', MobileValidator::class],
            [['token_type'], 'integer', 'max' => MemberAccessToken::MAX_TOKEN_TYPE],
        ];
    }

    public function scenarios()
    {
        $scenarios = [
            static::SCENARIO_OAUTH => ['oauth_name', 'oauth_key', 'token_type'],
            static::SCENARIO_SYSTEM => ['oauth_name', 'oauth_key'],
            static::SCENARIO_MOBILE => ['mobile', 'password'],
        ];
        return ArrayHelper::merge(parent::scenarios(), $scenarios);
    }

    /**
     * 统一登录入口
     * @return bool|MemberAccessToken
     */
    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        switch ($this->scenario) {
            case static::SCENARIO_OAUTH:
            case static::SCENARIO_SYSTEM:
                return $this->loginByOauth();
                break;
            default:
                $this->addError('scenario', 'scenario not exist');
                return false;
        }
    }

    /**
     * 第三方登录
     * @return bool|MemberAccessToken
     */
    protected function loginByOauth()
    {
        // 查找第三方名和key的用户
        if (!$memberOauth = MemberOauth::findMemberByNameAndKey($this->oauth_name, $this->oauth_key)) {
            $this->addError('oauth', Code::USER_OAUTH_KEY_NOT_FOUND);
            return false;
        }
        // 登录
        if (!Yii::$app->user->login($memberOauth->member, $memberOauth->member->getDuration())) {
            $this->addError('oauth', Code::SERVER_FAILED);
            return false;
        }

        // 生成access_token
        if (!$accessModel = $this->createAccessToken()) {
            return false;
        }
        return $accessModel;
    }

    /**
     * 手机号登录
     */
    protected function loginByMobile()
    {

    }

    /**
     * 生成access_token
     * @return bool|MemberAccessToken
     */
    protected function createAccessToken()
    {
        if ($this->scenario != static::SCENARIO_SYSTEM) {
            if (!$model = MemberAccessToken::createAccessToken($this->token_type)) {
                $this->addErrors($model->getErrors());
                return false;
            }
            return $model;
        }
    }


}