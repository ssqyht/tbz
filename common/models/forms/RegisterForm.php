<?php
/**
 * @user: thanatos
 */

namespace common\models\forms;


use common\components\traits\FuncTrait;
use common\extension\Code;
use common\models\FileCommon;
use common\models\FileUsedRecord;
use common\models\Member;
use Yii;
use common\models\CenterUser;
use common\models\MemberOauth;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\imagine\Image;

/**
 * 统一注册类
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class RegisterForm extends Model
{
    public $username;
    public $sex;
    public $headimgurl;
    public $oauth_key;
    public $oauth_name;

    const SCENARIO_OAUTH = 'oauth';

    public function rules()
    {
        return [
            [['username', 'sex', 'oauth_name', 'oauth_key'], 'required'],
            [['headimgurl'], 'string'],
            [['username'], 'string', 'max' => 30],
            [['sex'], 'integer', 'max' => Member::SEX_MAX, 'min' => 0],
            [['oauth_name'], 'integer', 'max' => MemberOauth::MAX_OAUTH_NAME, 'min' => 1],
            ['oauth_key', 'string', 'max' => 50],
            ['oauth_key', 'validateOauthKey'],
        ];
    }

    /**
     * 验证是否已经注册
     * @param $attribute
     * @param $params
     */
    public function validateOauthKey($attribute, $params)
    {
        if (MemberOauth::findByNameAndKey($this->oauth_name, $this->oauth_key)) {
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
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 创建用户
            $member = new Member();
            $member->username = $this->username;
            $member->sex = $this->sex;
            $member->status = Member::STATUS_NORMAL;
            // 生成头像
            if ($this->headimgurl && $result = FileUpload::upload($this->headimgurl, FileUpload::DIR_OTHER)) {
                $member->headimg_id = $result->file_id ?? 0;
                $member->headimg_url = $result->path ?? '';
            }
            // 保存
            if (!$member->save()) {
                throw new Exception('member save failed:'. Json::encode($member->getErrors()));
            }
            // 添加文件使用日志
            $usedModel = new FileUsedRecord(['scenario' => FileUsedRecord::SCENARIO_CREATE]);
            $usedModel->load([
                'user_id' => $member->id,
                'file_id' => $member->headimg_id,
                'purpose' => FileUsedRecord::PURPOSE_HEADIMG,
                'purpose_id' => $member->id,
            ], '');
            if (!$usedModel->submit()) {
                throw new Exception(json_encode($usedModel->getFirstErrors()));
            }

            // 创建第三方授权记录
            $memberOauth= new MemberOauth();
            $memberOauth->user_id = $member->id;
            $memberOauth->oauth_name = $this->oauth_name;
            $memberOauth->oauth_key = $this->oauth_key;
            if (!$memberOauth->save()) {
                throw new Exception('member oauth failed:'. Json::encode($memberOauth->getErrors()));
            }

            $transaction->commit();
            return $member;

        } catch (\Exception $e) {
            try {
                $this->addError('', $e->getMessage());
                $transaction->rollBack();
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }

    }

}