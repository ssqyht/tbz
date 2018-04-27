<?php
namespace api\controllers;

use common\components\traits\funcTraits;
use common\models\forms\FileUpload;
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
        echo 'You must visit a module  "/v1"';exit;
    }


}
