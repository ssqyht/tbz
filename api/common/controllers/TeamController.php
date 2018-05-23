<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/19
 * Time: 13:20
 */

namespace api\common\controllers;

use common\models\forms\MyFavoriteForm;
use common\models\MyFavorite;
use common\models\search\TeamSearch;
use common\models\TbzLetter;
use common\models\search\MyFavoriteSearch;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use common\models\forms\TeamForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class TeamController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/team",
     *     operationId="getTeam",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="获取团队信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="team_id",
     *          type="integer",
     *          description="团队的唯一标识team_id",
     *          required = true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Team")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return array|mixed|null
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $model = new TeamSearch();
        $result = $model->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }
    /**
     * @SWG\Post(
     *     path="/team",
     *     operationId="addTeam",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="添加团队",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="team_name",
     *          type="string",
     *          description="团队名称",
     *          required=true,
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Team"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|\common\models\Team
     * @throws BadRequestHttpException
     * 创建团队
     */
    public function actionCreate()
    {
        $create_data = \Yii::$app->request->post();
        $model = new TeamForm();
        if ($model->load($create_data, '') && ($result = $model->addTeam())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/team/{id}",
     *     operationId="updateTeam",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="编辑团队",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="团队唯一标识id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="team_name",
     *          type="string",
     *          description="团队名称",
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="team_mark",
     *          type="string",
     *          description="团队头像",
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Team"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @param $id
     * @return mixed
     * @throws BadRequestHttpException
     * 编辑团队信息
     */
    public function actionUpdate($id)
    {
        $update_data = \Yii::$app->request->post();
        $model = new TeamForm();
        if ($model->load($update_data, '') && ($result = $model->updateTeam($id))) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/team/{id}",
     *     operationId="deleteTeam",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="删除团队",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="团队唯一标识id",
     *          required=true,
     *     ),
     *      @SWG\Response(
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
     * @param $id
     * @return bool
     * @throws HttpException
     * 删除团队
     */
    public function actionDelete($id)
    {
        $model = new TeamForm();
        if ($model->deleteTeam($id)) {
            return true;
        }
        throw new HttpException(500, $model->getStringErrors(), Code::SERVER_FAILED);
    }
}