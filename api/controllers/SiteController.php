<?php
namespace api\controllers;

use common\components\traits\FuncTraits;
use common\models\forms\FileUpload;
use common\models\forms\LoginForm;
use common\models\forms\RegisterForm;
use common\models\MemberOauth;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new LoginForm(['scenario' => LoginForm::SCENARIO_OAUTH]);
        $model->load([
            'oauth_name' => MemberOauth::OAUTH_WECHAT,
            'oauth_key' => 'oABZht56ASGUupPlW8jRuczPHpdI'
        ], '');
        var_dump($model->submit());

        echo 'You must visit a module  "/v1"';exit;
    }


}
