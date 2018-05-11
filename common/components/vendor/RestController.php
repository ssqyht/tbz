<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\vendor;

use common\extension\Code;
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

    public function init()
    {
        parent::init();
        // 设置OSS图片网址别名
        Yii::setAlias('@oss', Yii::$app->params['ossUrl']);
        // 设置OSS内网图片网址别名
        Yii::setAlias('@ossInternal', Yii::$app->params['ossInternal']);
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

    /**
     * @param $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \yii\web\BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function beforeAction($action)
    {
        // 处理客户端Client信息
        if (!Yii::$app->request->isOptions) {
            $client_id = Yii::$app->request->headers->get('Client');
            $client = OauthPublicKeys::getClientById($client_id);
            if (empty($client)) {
                throw new ForbiddenHttpException('没有权限', Code::SERVER_NOT_PERMISSION);
            }
            $this->client = $client;
        }
        // 处理客户端handle信息
        $this->setHandle();
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ]
        ];
    }

    /**
     * 判断当前请求是不是前端请求
     * @return bool
     * @author thanatos <thanatos915@163.com>
     */
    public function isFrontend()
    {
        return $this->_handle == 'frontend';
    }

    /**
     * @author thanatos <thanatos915@163.com>
     */
    protected function setHandle()
    {
        $handle = Yii::$app->request->headers->get('Handle');
        $this->_handle = $handle == 'backend' ? $handle : 'frontend';
    }

}