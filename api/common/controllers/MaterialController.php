<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 17:07
 */

namespace api\common\controllers;

use common\models\forms\MaterialForm;
use common\models\forms\UpfileForm;
use common\models\search\MaterialSearch;
use common\models\TemplateMember;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\models\forms\MaterialOperationForm;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class MaterialController extends BaseController
{
    /**
     * @SWG\Get(
     *     path="/material",
     *     operationId="getMaterial",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="根据条件查询素材",
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
     *       @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="素材状态,10正常，7回收站，3删除",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="sort",
     *          type="integer",
     *          description="按创建时间排序，默认降序，1为升序",
     *     ),
     *       @SWG\Parameter(
     *          in="query",
     *          name="folder",
     *          type="integer",
     *          description="所在文件夹的id",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/MaterialMember")
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
        //\Yii::$app->cache->flush();die;
        $model = new MaterialSearch();
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MaterialSearch::MATERIAL_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MaterialSearch::MATERIAL_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->get(), $method);
        $result = $model->search($data);
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * 新增素材
     * @SWG\POST(
     *     path="/material",
     *     operationId="addMaterial",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="新增素材",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/MaterialMember")
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                      property="classify_name",
     *                      type="array",
     *                      @SWG\Items(ref="#/definitions/MaterialMember")
     *                  ))
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|\common\models\MaterialMember|\common\models\MaterialTeam
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MaterialSearch::MATERIAL_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MaterialSearch::MATERIAL_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $model = new MaterialForm();
        if ($model->load($data, '') && ($result = $model->addMaterial())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * 编辑素材
     * @SWG\Put(
     *     path="/material/{id}",
     *     operationId="updateMaterial",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="编辑素材",
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
     *        name="id",
     *        in="path",
     *        required=true,
     *        type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/MaterialMember")
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                      property="classify_name",
     *                      type="array",
     *                      @SWG\Items(ref="#/definitions/MaterialMember")
     *                  ))
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     *
     * @param $id
     * @return bool|\common\models\MaterialMember|\common\models\MaterialTeam|null
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MaterialSearch::MATERIAL_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MaterialSearch::MATERIAL_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $model = new MaterialForm();
        if ($model->load($data, '') && ($result = $model->updateMaterial($id))) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/material/{id}",
     *     operationId="deleteMaterial",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="素材放入回收站",
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
     *         name="Handle",
     *         in="header",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        type="integer"
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
     * @param $id
     * @return bool
     * @throws HttpException
     */
    public function actionDelete($id)
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MaterialSearch::MATERIAL_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MaterialSearch::MATERIAL_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $model = new MaterialForm();
        if ($model->load($data, '') && ($result = $model->deleteMaterial($id))) {
            return $result;
        }
        throw new HttpException(500, $model->getStringErrors(), Code::SERVER_FAILED);
    }

    /**
     * @SWG\POST(
     *     path="/material/material-operation",
     *     operationId="materialOperation",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="素材的常规操作(单个重命名，删除，到回收站、还原、移动到文件夹)",
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
     *          name="type",
     *          type="integer",
     *          description="操作类型,1重命名(单个),2移动到文件夹，3到回收站，4删除，5还原",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="ids",
     *          type="integer",
     *          description="素材的唯一标识，单操作时为integer，多操作时为数组",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="文件名称,type为1时必传",
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="folder",
     *          type="integer",
     *          description="文件夹的id,type为2必传",
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
     * @return bool|null
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionMaterialOperation()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => MaterialSearch::MATERIAL_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => MaterialSearch::MATERIAL_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $model = new MaterialOperationForm();
        if ($result = $model->operation($data)) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }
}