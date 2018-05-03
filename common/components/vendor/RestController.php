<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\vendor;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;

class RestController extends Controller
{
    public function behaviors(){
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors'  =>[
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
            'optional' =>  ['*']
        ];
        return $behaviors;
    }

    public function actions(){
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ]
        ];
    }

}