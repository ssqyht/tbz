<?php
/**
 * Created by PhpStorm.
 * User: thanatos
 * Date: 2018/4/25
 * Time: 上午3:11
 */

namespace api\common\models\wechat;

use common\models\MemberOauth;
use common\models\forms\RegisterForm;
use common\models\Member;
use Yii;
use thanatos\wechat\MessageHandler;

class BaseMessageHandle extends MessageHandler
{

    public function beforeHandle()
    {
        // 同步用户信息
        return parent::beforeHandle();
    }

    public function handleDefault()
    {

    }

    public function autoLogin()
    {
//        if (Yii::$app->user->isGuest) {
//            $member = Member::find()->where();
//        }
    }

}