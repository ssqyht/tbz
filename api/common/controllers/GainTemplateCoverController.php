<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\forms\TbzSubjectForm;
use common\models\TbzSubject;
use Yii;
use common\components\vendor\RestController;
use yii\web\NotFoundHttpException;
use common\models\search\TbzSubjectSearch;
use common\extension\Code;
use yii\web\BadRequestHttpException;

class GainTemplateCoverController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/gain-template-cover",
     *     operationId="GetCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="获取专题模板列表信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否上线",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TbzSubject")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return array|\common\components\vendor\Response|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @author swz
     */
    public function actionIndex()
    {
        $tbz_subject = new TbzSubjectSearch();
        $result_data = $tbz_subject->search(\Yii::$app->request->get());
        if ($result_data) {
            return $result_data;
        } else {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
    }

    /**
     * @SWG\Post(
     *     path="/gain-template-cover",
     *     operationId="AddCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="添加新的专题模板",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="title",
     *          type="string",
     *          description="模板标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="description",
     *          type="string",
     *          description="专题描述",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="banner",
     *          type="string",
     *          description="专题内页banner图",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="thumbnail",
     *          type="string",
     *          description="缩略图",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_title",
     *          type="string",
     *          description="seo标题",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_keyword",
     *          type="string",
     *          description="seo关键词",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_description",
     *          type="string",
     *          description="seo描述",
     *          required=true,
     *     ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否上线",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序逆序",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TbzSubject"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|TbzSubject
     * @throws BadRequestHttpException
     * @author swz
     */
    public function actionCreate()
    {
        $add_data = \Yii::$app->request->post();
        $tbz_subject = new TbzSubjectForm();
        if ($tbz_subject->load($add_data, '') && ($result = $tbz_subject->TbzSubjectAdd())) {
            return $result;
        } else {
            throw new BadRequestHttpException('', Code::SERVER_UNAUTHORIZED);
        }
    }

    /**
     * @SWG\Put(
     *     path="/gain-template-cover/{id}",
     *     operationId="UpdateCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="修改专题模板信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *       @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="模板标识",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="title",
     *          type="string",
     *          description="模板标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="description",
     *          type="string",
     *          description="专题描述",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="banner",
     *          type="string",
     *          description="专题内页banner图",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="thumbnail",
     *          type="string",
     *          description="缩略图",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_title",
     *          type="string",
     *          description="seo标题",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_keyword",
     *          type="string",
     *          description="seo关键词",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="seo_description",
     *          type="string",
     *          description="seo描述",
     *          required=true,
     *     ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否上线",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序逆序",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TbzSubject"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|array|\common\components\vendor\Response|\yii\console\Response
     * @throws BadRequestHttpException
     * @author swz
     */
    public function actionUpdate($id)
    {
        $update_data = \Yii::$app->request->post();
        $tbz_subject = new TbzSubjectForm();
        if ($tbz_subject->load($update_data, '') && ($result = $tbz_subject->TbzSubjectUpdate($id))) {
            return $result;
        }
        throw new BadRequestHttpException('', Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/gain-template-cover/{id}",
     *     operationId="deleteCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="删除专题模板信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *       @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="模板标识",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Property(
     *                      property="content",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|\common\components\vendor\Response|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @author swz
     */
    public function actionDelete($id)
    {
        $tbz_subject = new TbzSubjectForm();
        if ($tbz_subject->TbzSubjectDelete($id)) {
            return true;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /*
     * 迁移老数据
     */
    public function actionMigrateOldData()
    {
        $test_data = \Yii::$app->db_old->createCommand('SELECT * FROM com_tbz_subject ')
            ->queryAll();
        $data = [];
        $i = 0;
        foreach ($test_data as $key => $value) {
            $data[$i][0] = $value['id'];
            $data[$i][1] = $value['title'];
            $data[$i][2] = $value['description'];
            $data[$i][3] = $value['thumbnail'];
            $data[$i][4] = $value['banner'];
            $data[$i][5] = $value['seoTitle'];
            $data[$i][6] = $value['seoKeyword'];
            $data[$i][7] = $value['seoDescription'];
            $data[$i][8] = $value['status'];
            $data[$i][9] = $value['sort'];
            $data[$i][10] = strtotime($value['createdTime']);
            $data[$i][11] = strtotime($value['updatedTime']);
            $i++;
        }
        Yii::$app->db->createCommand()->batchInsert('tbz_subject', ['id', 'title', 'description', 'thumbnail', 'banner', 'seo_title', 'seo_keyword', 'seo_description', 'status', 'sort', 'created_time', 'updated_time'], $data)->execute();//执行批量添加
        return $data;
    }
}