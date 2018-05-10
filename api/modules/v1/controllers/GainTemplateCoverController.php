<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\modules\v1\controllers;

use common\models\forms\TbzSubjectForm;
use common\models\TbzSubject;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\NotFoundHttpException;
use common\models\search\TbzSubjectSearch;
use common\extension\Code;

class GainTemplateCoverController extends \api\common\controllers\UserController
{
    /**
     * @SWG\Post(
     *     path="/gain-template-cover/get-cover",
     *     operationId="GetCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="获取专题模板列表信息",
     *      @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否上线,0为线下，1为上线",
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
     * @return array|\common\components\vendor\Response|\yii\console\Response|Response
     * @author swz
     */
    public function actionGetCover()
    {
        $status = trim(\Yii::$app->request->post('status'));
        if (!isset($status) || $status == '') {
            $status = 1;
        }
        $tbz_subject = new TbzSubjectSearch();
        $result_data = $tbz_subject->search($status);
        if ($result_data) {
            return $result_data;
        } else {
            throw new NotFoundHttpException('获取专题模板失败');
        }
    }

    /**
     * @SWG\Post(
     *     path="/gain-template-cover/add-cover",
     *     operationId="AddCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="添加新的专题模板",
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
     * @return array|\common\components\vendor\Response|\yii\console\Response|Response
     * @author swz
     */
    public function actionAddCover()
    {
        $add_data = \Yii::$app->request->post();
        $tbz_subject = new TbzSubjectForm();
        $tbz_subject->attributes = $add_data;
        if (!$tbz_subject->validate()) {
            throw new NotFoundHttpException('信息有误');
        }
        if ($result = $tbz_subject->TbzSubjectAdd($add_data)) {
            return $result;
        } else {
            throw new NotFoundHttpException('添加模板失败');
        }

    }

    /**
     * @SWG\Post(
     *     path="/gain-template-cover/update-cover",
     *     operationId="UpdateCover",
     *     schemes={"http"},
     *     tags={"专题模板相关接口"},
     *     summary="修改专题模板信息",
     *       @SWG\Parameter(
     *          in="formData",
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
     * @return array|\common\components\vendor\Response|\yii\console\Response|Response
     * @author swz
     */
    public function actionUpdateCover()
    {
        $update_data = \Yii::$app->request->post();
        $id = $update_data['id'];
        if (!$id) {
            throw new NotFoundHttpException('id不能为空');
        }
        $tbz_subject = new TbzSubjectForm();
        $tbz_subject->attributes = $update_data;
        if (!$tbz_subject->validate()) {
            throw new NotFoundHttpException('信息有误');
        }
        $result = $tbz_subject->TbzSubjectUpdate($update_data);
        if ($result) {
            return $result;
        } else {
            throw new NotFoundHttpException('修改信息失败');
        }
    }

    public function actionDeleteCover()
    {
        $id = trim(\Yii::$app->request->post('id'));
        if (!$id) {
            throw new NotFoundHttpException('id不能为空');
        }
        $com_tbz_subject = TbzSubject::findOne($id);
        if (!$com_tbz_subject) {
            throw new NotFoundHttpException('当前操作的模板不存在');
        }
        $result = TbzSubject::deleteAll(['id' => $id]);
        if ($result) {
            return ['message' => '删除模板成功'];
        } else {
            throw new NotFoundHttpException('删除专题模板失败');
        }
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