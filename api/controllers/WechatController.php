<?php
/**
 * Created by PhpStorm.
 * User: thanatos
 * Date: 2018/4/23
 * Time: 下午1:41
 */

namespace api\controllers;


use api\common\models\wechat\EventMessageHandle;
use Yii;
use yii\web\Controller;

class WechatController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function actionServer()
    {
        $app = Yii::$app->wechat->app;

        $app->server->push(EventMessageHandle::class);

        $response = $app->server->serve();

        $response->send();
    }

}
