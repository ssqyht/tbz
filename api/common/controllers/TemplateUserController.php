<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\forms\TemplateForm;
use common\models\search\TemplateUserSearch;
use common\models\TemplateMember;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\models\forms\TemplateOperationForm;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;

class TemplateUserController extends BaseController
{

    /**
     * @SWG\Get(
     *     path="/template-user",
     *     operationId="getTemplateUser",
     *     schemes={"http"},
     *     tags={"用户模板相关接口"},
     *     summary="根据条件查询用户模板信息(团队和个人)",
     *     description="此接口为前后台根据查询条件查询团队或个人模板信息，成功返回相应的模板信息，无查询条件时默认返回默认文件下的正常模板信息，有分页，（此接口可用于展示模板信息页和回收站页，根据状态查询）",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台",
     *     ),
     *     @SWG\Parameter(
     *         name="team",
     *         in="header",
     *         type="integer",
     *         description="团队的唯一标识team_id,当为查询团队版模板时，此项必传，否则查询结果为个人模板信息",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="前后台的查询条件，模板状态,10正常,7回收站,3删除，默认展示正常模板（即值为10），值为7时可用于回收站的展示",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="sort",
     *          type="integer",
     *          description="前后台的查询条件，按创建时间排序，默认降序，1为升序",
     *     ),
     *       @SWG\Parameter(
     *          in="query",
     *          name="folder",
     *          type="integer",
     *          description="前台查询条件（后台不用），所在文件夹的唯一标识folder_id,默认显示默认文件的内容",
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="classify_id",
     *          type="string",
     *          description="前后台查询条件，小分类的classify_id",
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
        if ($team_id = \Yii::$app->request->headers->get('team')){
            //团队
            $method = ['method' => TemplateUserSearch::TEMPLATE_TEAM,'team_id'=>$team_id];
        }else{
            //个人
            $method = ['method' => TemplateUserSearch::TEMPLATE_MEMBER];
        }
        $template_member = new TemplateUserSearch();
        $data = ArrayHelper::merge(\Yii::$app->request->get(),$method);
        $result = $template_member->search($data);
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
     *     path="/template-user/template-operation",
     *     operationId="templateUserOperation",
     *     schemes={"http"},
     *     tags={"用户模板相关接口"},
     *     summary="用户（团队、个人）模板的常规操作(单个重命名，删除，到回收站、还原、个人转团队、移动到文件夹)",
     *     description="此接口用于前台个人或团队模板的重命名、到回收站、删除、还原、移动到指定文件夹等场景",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台（此接口只支持前台）",
     *     ),
     *     @SWG\Parameter(
     *         name="team",
     *         in="header",
     *         type="integer",
     *         description="团队的唯一标识team_id,当为团队模板的操作时，此项必传，否则为操作当前用户的模板",
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
     *          description="模板的唯一标识template_id的值，为template_id组成的数组",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="文件名称,重命名时（即type为1时）必传",
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="folder",
     *          type="integer",
     *          description="文件夹的id,移动到指定文件夹时（即type为2时）必传",
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
        $model = new TemplateOperationForm();
        if ($team_id = \Yii::$app->request->headers->get('team')){
            //团队
            $method = ['method' => TemplateOperationForm::TEMPLATE_TEAM,'team_id'=>$team_id];
        }else{
            //个人
            $method = ['method' => TemplateOperationForm::TEMPLATE_MEMBER];
        }
        $data = ArrayHelper::merge(\Yii::$app->request->post(),$method);
        if ($result = $model->operation($data)) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

}