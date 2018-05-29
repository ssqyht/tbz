<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\controllers;


use Yii;
use yii\web\Controller;

class OssController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionCallback()
    {
        $data = Yii::$app->request->post();
        $data = json_decode(file_get_contents('1.txt'), true);



        return json_encode($data);
    }
}