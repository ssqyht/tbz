<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\forms\TemplateForm;
use common\models\search\TemplateMemberSearch;
use common\models\TemplateMember;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\models\forms\BasicOperationForm;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;

class TemplateMemberController extends BaseController
{

    /**
     * @SWG\Get(
     *     path="/Template-member",
     *     operationId="getTemplateMember",
     *     schemes={"http"},
     *     tags={"用户相关接口"},
     *     summary="根据条件查询模板信息",
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
     *      @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="模板状态,10正常,7回收站,3删除",
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
     *          description="所在文件夹的id,默认显示默认文件的内容",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="product",
     *          type="string",
     *          description="小分类",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TemplateMember")
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
        $template_member = new TemplateMemberSearch();
        $result = $template_member->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }
    /**
     * 新增模板
     * @SWG\POST(
     *     path="/template-member",
     *     operationId="addTemplateMember",
     *     schemes={"http"},
     *     tags={"个人模板接口"},
     *     summary="新增个人模板",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TemplateMember")
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
     *                      @SWG\Items(ref="#/definitions/TemplateMember")
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
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $model = new TemplateForm();
        $data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => TemplateForm::METHOD_SAVE_MEMBER]);
        if (!($result = $model->submit($data))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
        return $result;
    }

    /**
     * 保存官方模板
     * @SWG\Put(
     *     path="/template-member/{templateId}",
     *     operationId="updateTemplateMember",
     *     schemes={"http"},
     *     tags={"个人模板接口"},
     *     summary="编辑个人模板",
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
     *        name="templateId",
     *        in="path",
     *        required=true,
     *        type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TemplateMember")
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
     *                      @SWG\Items(ref="#/definitions/TemplateMember")
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
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = new TemplateForm();
        $data = ArrayHelper::merge(\Yii::$app->request->post(), ['template_id' => $id, 'method' => TemplateForm::METHOD_SAVE_MEMBER]);
        //return $data;
        if (!($result = $model->submit($data))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
        return $result;
    }

    /**
     * @SWG\Delete(
     *     path="/template-member/{templateId}",
     *     operationId="deleteTemplateMember",
     *     schemes={"http"},
     *     tags={"个人模板接口"},
     *     summary="删除个人模板",
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
     *        name="templateId",
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
     *
     *
     * @param integer $id
     * @throws BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionDelete($id)
    {
        $model = new TemplateForm();
        $data = ['template_id' => $id, 'method' => TemplateForm::METHOD_SAVE_MEMBER, 'status' => TemplateMember::STATUS_DELETE];
        if (!($result = $model->submit($data))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
    }

    /**
     * @SWG\POST(
     *     path="/template-member/template-operation",
     *     operationId="templateMemberOperation",
     *     schemes={"http"},
     *     tags={"个人模板接口"},
     *     summary="个人模板的常规操作(单个重命名，删除，到回收站、还原、个人转团队、移动到文件夹)",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
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
     *          type="string",
     *          description="模板的唯一标识template_id的值，单操作时为integer，多操作时为template_id组成的数组",
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
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionTemplateOperation()
    {
        $model = new BasicOperationForm();
        $data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => BasicOperationForm::TEMPLATE_MEMBER]);
        if ($result = $model->operation($data)) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

}