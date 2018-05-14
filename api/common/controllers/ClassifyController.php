<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\extension\Code;
use common\models\Classify;
use Yii;
use common\components\vendor\RestController;
use common\models\search\ClassifySearch;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

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


    /**
     * 修改分类
     * @param $id
     * @return Classify|null
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionUpdate($id)
    {
        $model = Classify::findById($id);
        if (empty($model)) {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        $model->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return $model;
        } else {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
    }

    /**
     * 新增分类
     * @return Classify
     * @throws BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionCreate()
    {
        $model = new Classify();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $model;
        } else {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
    }

    /**
     * 删除分类
     * @param $id
     * @throws NotFoundHttpException
     * @throws \HttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionDelete($id)
    {
        $model = Classify::findById($id);
        if (empty($model)) {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        try {
            $model->delete();
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }
        if ($message)
            throw new \HttpException(500, Code::SERVER_FAILED);
    }

}