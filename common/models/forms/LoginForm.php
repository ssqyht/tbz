<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

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

    public $oauth_name;
    public $oauth_key;
    public $mobile;
    public $password;

    public function rules()
    {
        return [
            [['oauth_name', 'oauth_key', 'mobile', 'password'], 'required'],
            [['oauth_name'], 'integer', 'max' => MemberOauth::MAX_OAUTH_NAME, 'min' => 1],
            [['oauth_key'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 11],
            ['mobile', MobileValidator::class],
            // TODO 添加password
        ];
    }

    public function scenarios()
    {
        $scenarios = [
            static::SCENARIO_OAUTH => ['oauth_name', 'oauth_key'],
            static::SCENARIO_MOBILE => ['mobile', 'password'],
        ];
        return ArrayHelper::merge(parent::scenarios(), $scenarios);
    }

    /**
     * 统一登录入口
     * @return bool
     */
    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        switch ($this->scenario) {
            case static::SCENARIO_OAUTH:
                return $this->loginByOauth();
                break;
            default:
                $this->addError('scenario', 'scenario not exist');
                return false;
        }
    }

    /**
     * 第三方登录
     * @return bool
     */
    protected function loginByOauth()
    {
        if (!$memberOauth = MemberOauth::findMemberByNameAndKey($this->oauth_name, $this->oauth_key)) {
            $this->addError('oauth', Code::USER_OAUTH_KEY_NOT_FOUND);
            return false;
        }
        if (!Yii::$app->user->login($memberOauth->member, $memberOauth->member->getDuration())) {
            $this->addError('oauth', Code::SERVER_FAILED);
            return false;
        }
        return true;
    }

    /**
     * 手机号登录
     */
    protected function loginByMobile()
    {

    }



}