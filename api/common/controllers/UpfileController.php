<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/17
 * Time: 21:49
 */

namespace api\common\controllers;

use common\models\forms\UpfileForm;
use common\models\search\UpfileSearch;
use common\models\TemplateMember;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\models\forms\BasicOperationForm;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
class UpfileController extends BaseController
{
    /**
     * @SWG\Get(
     *     path="/Upfile",
     *     operationId="getUpfile",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="根据条件查询素材",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
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
     *                  @SWG\Items(ref="#/definitions/Upfile")
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
        $up_file = new UpfileSearch();
        $result = $up_file->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * 新增素材
     * @SWG\POST(
     *     path="/upfile",
     *     operationId="addUpfile",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Upfile")
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
     *                      @SWG\Items(ref="#/definitions/Upfile")
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
     * @return bool|\common\models\Upfile
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $create_data = \Yii::$app->request->post();
        $model = new UpfileForm();
        if ($model->load($create_data, '') && ($result = $model->addUpfile())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * 编辑素材
     * @SWG\Put(
     *     path="/upfile/{id}",
     *     operationId="updateUpfile",
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
     *        name="id",
     *        in="path",
     *        required=true,
     *        type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Upfile")
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
     *                      @SWG\Items(ref="#/definitions/Upfile")
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
     * @return bool|TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $update_data = \Yii::$app->request->post();
        $model = new UpfileForm();
        if ($model->load($update_data, '') && ($result = $model->updateUpfile($id))) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/upfile/{id}",
     *     operationId="deleteTemplateUpfile",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="把素材放进回收站",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
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
        $message = new UpfileForm();
        if ($result = $message->deleteUpfile($id)) {
            return $result;
        }
        throw new HttpException(500, $message->getStringErrors(), Code::SERVER_FAILED);
    }

    /**
     * @SWG\POST(
     *     path="/upfile/upfile-operation",
     *     operationId="templateUpfileOperation",
     *     schemes={"http"},
     *     tags={"素材接口"},
     *     summary="素材的常规操作(单个重命名，删除，到回收站、还原、个人转团队、移动到文件夹)",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     )
     *     @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="操作类型,1重命名(单个),2移动到文件夹，3到回收站，4删除，5还原，6个人转团队",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="ids",
     *          type="array",
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
     *     @SWG\Parameter(
     *          in="formData",
     *          name="team_id",
     *          type="integer",
     *          description="团队id,type为6时必传",
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
    public function actionUpfileOperation()
    {
        $model = new BasicOperationForm();
        $data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => BasicOperationForm::UPFILE]);
        if ($result = $model->operation($data)) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }
}