<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/19
 * Time: 9:08
 */

namespace api\common\controllers;

use common\models\forms\MyFavoriteForm;
use common\models\MyFavorite;
use common\models\TbzLetter;
use common\models\search\MyFavoriteSearch;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use common\models\forms\MessageForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class MyFavoriteController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/my-favorite",
     *     operationId="getMyFavorite",
     *     schemes={"http"},
     *     tags={"收藏接口"},
     *     summary="获取收藏模板信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="team",
     *         in="header",
     *         type="integer"
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="sort",
     *          type="integer",
     *          description="按时间排序，默认降序，1为升序",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="classify_id",
     *          type="integer",
     *          description="小分类的classify_id",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TemplateOfficial")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|mixed|null
     * @throws NotFoundHttpException
     * 查询收藏
     */
    public function actionIndex()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MyFavoriteSearch::FAVORITE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MyFavoriteSearch::FAVORITE_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->get(), $method);
        $model = new MyFavoriteSearch();
        $result = $model->search($data);
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/my-favorite",
     *     operationId="addMyFavorite",
     *     schemes={"http"},
     *     tags={"收藏接口"},
     *     summary="添加收藏",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="team",
     *         in="header",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="template_id",
     *          type="integer",
     *          description="模板的template_id",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     * 添加收藏
     */
    public function actionCreate()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MyFavoriteForm::FAVORITE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MyFavoriteForm::FAVORITE_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $model = new MyFavoriteForm();
        if ($model->load($data, '') && ($result = $model->addMyFavorite())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    public function actionUpdate()
    {

    }

    /**
     * @SWG\Delete(
     *     path="/my-favorite/{id}",
     *     operationId="deleteMyFavorite",
     *     schemes={"http"},
     *     tags={"收藏接口"},
     *     summary="删除收藏",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="team",
     *         in="header",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="收藏id",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @param $id
     * @return bool
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MyFavoriteForm::FAVORITE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MyFavoriteForm::FAVORITE_MEMBER];
        }
        $model = new MyFavoriteForm();
        if ($model->load($method, '') && $model->deleteMyFavorite($id)) {
            return true;
        }
        throw new HttpException(500, $model->getStringErrors(), Code::SERVER_FAILED);
    }
}