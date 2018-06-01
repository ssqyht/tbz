<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\vendor;

use common\extension\Code;
use common\models\Member;
use common\models\OauthPublicKeys;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
class RestController extends Controller
{
    /** @var OauthPublicKeys */
    public $client;

    private $_handle;

    /**
     * @throws ForbiddenHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function init()
    {
        \Yii::$app->user->login(Member::findIdentity(1));
        parent::init();
        // 设置OSS图片网址别名
        Yii::setAlias('@oss', Yii::$app->params['ossUrl']);
        // 设置OSS内网图片网址别名
        Yii::setAlias('@ossInternal', Yii::$app->params['ossInternal']);

       /* if (!Yii::$app->request->isOptions && Yii::$app->request->client === false) {
            throw new ForbiddenHttpException('没有仅限', Code::SERVER_NOT_PERMISSION);
        }*/

    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => Yii::$app->params['Origin'],
                'Access-Control-Request-Method' => Yii::$app->params['Access-Control-Request-Method'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => "*",
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => Yii::$app->params['Access-Control-Expose-Headers'],
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['*']
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ]
        ];
    }

}