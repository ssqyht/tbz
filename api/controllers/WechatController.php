<?php
/**
 * Created by PhpStorm.
 * User: thanatos
 * Date: 2018/4/23
 * Time: 下午1:41
 */

namespace api\controllers;


use api\common\models\wechat\EventMessageHandle;
use common\components\traits\FuncTraits;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

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

    /**
     * 带参数二维码
     */
    public function actionQrcode()
    {
        $app = Yii::$app->wechat->app;
        $result = $app->qrcode->temporary('login', 3600);
        $url = $app->qrcode->url($result->ticket);

        $content = FuncTraits::getSourceOrigin($url);
        // Ajax 返回base64
        if (Yii::$app->request->isAjax) {
            return Json::encode(['content' => FuncTraits::base64Image($content)]);
        }
        // 直接输出图片
        $response = Yii::$app->response;
        $response->headers->set('Content-type', $content['mime']);
        $response->format = Response::FORMAT_RAW;
        $response->data = $content['content'];
        return $response;
    }

}
