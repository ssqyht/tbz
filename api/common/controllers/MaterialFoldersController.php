<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/18
 * Time: 13:40
 */
namespace api\common\controllers;

use common\models\forms\FolderForm;
use common\models\search\FolderSearch;
use common\models\TbzLetter;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
class MaterialFoldersController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/material-folders",
     *     operationId="getMaterialFolder",
     *     schemes={"http"},
     *     tags={"素材文件夹接口"},
     *     summary="获取素材文件夹信息",
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
     *          description="文件夹状态(后台时可以根据此参数查询,10为正常，7为到回收站，3为删除)",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/MaterialFolders")
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
        $folder = new FolderSearch();
        $data = ArrayHelper::merge(\Yii::$app->request->get(), ['method' => FolderSearch::MATERIAL_FOLDER]);
        $result = $folder->search($data);
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/material-folder",
     *     operationId="addMaterialFolder",
     *     schemes={"http"},
     *     tags={"素材文件夹接口"},
     *     summary="创建素材文件夹",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
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
     *                  ref="#/definitions/MaterialFolders"
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
        $create_data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => FolderForm::MATERIAL_FOLDER]);
        $message = new FolderForm();
        if ($message->load($create_data, '') && ($result = $message->addFolder())) {
            return $result;
        }
        throw new BadRequestHttpException($message->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/material-folder/{folder_id}",
     *     operationId="updateMaterialFolder",
     *     schemes={"http"},
     *     tags={"素材文件夹接口"},
     *     summary="编辑素材文件夹信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
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
     *                  ref="#/definitions/MaterialFolders"
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
        $update_data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => FolderForm::MATERIAL_FOLDER]);
        $folder = new FolderForm();
        if ($folder->load($update_data, '') && ($result = $folder->updateFolder($id))) {
            return $result;
        }
        throw new BadRequestHttpException($folder->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/material-folder/{folder_id}",
     *     operationId="deleteMaterialFolder",
     *     schemes={"http"},
     *     tags={"素材文件夹接口"},
     *     summary="素材文件夹到回收站",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
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
        $folder = new FolderForm();
        if ($folder->load(['method'=>FolderForm::MATERIAL_FOLDER], '') && $folder->deleteFolder($id)) {
            return true;
        }
        throw new HttpException(500, $folder->getStringErrors(), Code::SERVER_FAILED);
    }
}