<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;


use Yii;
use common\components\vendor\RestController;

class MainController extends RestController
{

    public function actionSendSms()
    {
        $mobile = Yii::$app->request->post('mobile');
        $type = Yii::$app->request->post('type');

        $smsModel = Yii::$app->sms;
        $result = $smsModel->send($mobile, $type);
        var_dump($smsModel->getErrors());exit;

    }

}