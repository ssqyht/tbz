<?php

namespace api\controllers;

use common\components\traits\FuncTraits;
use common\models\forms\FileUpload;
use common\models\forms\LoginForm;
use common\models\forms\RegisterForm;
use common\models\MemberOauth;
use Firebase\JWT\JWT;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function actions()
    {
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/site/api'], true),
                'title' => '图帮主接口',
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    Yii::getAlias('@api/common/controllers'),
                    Yii::getAlias('@api/common/swagger'),
                    Yii::getAlias('@api/modules/v1/swagger'),
                    Yii::getAlias('@api/modules/v1/controllers'),
                ],
                //The security key
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo 'You must visit a module  "/v1"';
        exit;
    }


}
