<?php
/**
 * Created by PhpStorm.
 * User: swz
 * Date: 2018/5/11
 * Time: 11:34
 */

namespace api\common\controllers;

use common\models\forms\FolderTemplateForm;
use common\models\search\FolderTemplateSearch;
use common\models\TbzLetter;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class FolderTemplateController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/folder-template",
     *     operationId="getFolderTemplate",
     *     schemes={"http"},
     *     tags={"文件夹接口"},
     *     summary="获取模板文件夹信息",
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
     *         name="Handle",
     *         in="header",
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="文件夹状态(后台可以根据状态查询,10为正常，7为回收站，3为删除)",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Folder")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_MEMBER];
        }
        $folder = new FolderTemplateSearch();
        $data = ArrayHelper::merge(\Yii::$app->request->get(), $method);
        $result = $folder->search($data);
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/folder-template",
     *     operationId="addFolderTemplate",
     *     schemes={"http"},
     *     tags={"文件夹接口"},
     *     summary="创建模板文件夹",
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
     *          name="name",
     *          type="string",
     *          description="文件夹名称",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="color",
     *          type="string",
     *          description="颜色",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Folder"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_MEMBER];
        }
        $create_data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $message = new FolderTemplateForm();
        if ($message->load($create_data, '') && ($result = $message->addFolder())) {
            return $result;
        }
        throw new BadRequestHttpException($message->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/folder-template/{folder_id}",
     *     operationId="updateFolderTemplate",
     *     schemes={"http"},
     *     tags={"文件夹接口"},
     *     summary="修改模板文件夹信息",
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
     *          name="folder_id",
     *          type="integer",
     *          description="文件夹id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="文件夹名称",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="color",
     *          type="string",
     *          description="颜色",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TbzLetter"
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
     * @return bool|TbzLetter|null
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        if ($team_id = \Yii::$app->request->headers->get('team')) {
            //团队
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_MEMBER];
        }
        $update_data = ArrayHelper::merge(\Yii::$app->request->post(), $method);
        $folder = new FolderTemplateForm();
        if ($folder->load($update_data, '') && ($result = $folder->updateFolder($id))) {
            return $result;
        }
        throw new BadRequestHttpException($folder->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/folder-template/{delete_id}",
     *     operationId="deleteFolderTemplate",
     *     schemes={"http"},
     *     tags={"文件夹接口"},
     *     summary="模板文件夹到回收站",
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
     *          name="folder_id",
     *          type="integer",
     *          description="文件夹id",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="true",
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
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_TEAM, 'team_id' => $team_id];
        } else {
            //个人
            $method = ['method' => FolderTemplateSearch::FOLDER_TEMPLATE_MEMBER];
        }
        $folder = new FolderTemplateForm();
        if ($folder->load($method, '') && $folder->deleteFolder($id)) {
            return true;
        }
        throw new HttpException(500, $folder->getStringErrors(), Code::SERVER_FAILED);
    }
}