<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use Yii;
use common\components\vendor\RestController;
use common\models\search\ClassifySearch;

class ClassifyController extends RestController
{

    /**
     * 查询官方模板分类
     * @return \yii\data\ActiveDataProvider
     * @author thanatos <thanatos915@163.com>
     */
    public function actionIndex()
    {
        $model = new ClassifySearch();
        $result = $model->search(Yii::$app->request->get());
        return $result;
    }


    public function actionUpdate()
    {
    }

    public function actionCreate()
    {
        
    }

}