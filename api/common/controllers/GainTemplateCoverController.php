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
     *     tags={"模板相关接口"},
     *     summary="获取专题模板列表信息",
     *     description="此接口是查看专题模板信息的接口，前台返回上线模板信息，后台可根据状态值查询专题模板，成功返回专题模板信息，有分页",
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
     *      @SWG\Parameter(
     *          in="query",
     *          name="status",
     *          type="integer",
     *          description="后台查询条件，7为回收站，10为线下，20为线上",
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
     * @return array|\common\components\vendor\Response|\yii\console\Response
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
     * @SWG\Get(
     *     path="/gain-template-cover/{id}",
     *     operationId="GetCoverOne",
     *     schemes={"http"},
     *     tags={"模板相关接口"},
     *     summary="获取单个专题模板信息",
     *     description="此接口用于查看单个专题模板信息,前台成功返回线上模板信息，后台无限制",
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
     *      @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="专题模板唯一标识id",
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
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     */
    public function actionView($id){
        $result = TbzSubject::findById($id);
        if ($result){
            return $result;
        }
        throw new BadRequestHttpException('未找到', Code::SERVER_UNAUTHORIZED);
    }
    /**
     * @SWG\Post(
     *     path="/gain-template-cover",
     *     operationId="AddCover",
     *     schemes={"http"},
     *     tags={"模板相关接口"},
     *     summary="添加新的专题模板",
     *     description="此接口是后台管理者添加专题模板的接口，成功返回所添加的专题模板信息",
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
     *          description="是否上线，10为线下，20为线上",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="热度，值越大热度越高",
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
     *     tags={"模板相关接口"},
     *     summary="修改专题模板信息",
     *     description="此接口是后台管理者修改专题模板的接口，成功返回所修改的专题模板信息",
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
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="所要修改的模板唯一标识id",
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
     *          description="是否上线，10为线下，20为线上",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="热度，值越大热度越高",
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
     *     tags={"模板相关接口"},
     *     summary="删除专题模板信息",
     *     description="此接口是后台管理者删除专题模板的接口，成功返回空字符串",
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
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="所要删除的模板唯一标识id",
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
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $tbz_subject = new TbzSubjectForm();
        if ($tbz_subject->TbzSubjectDelete($id)) {
            return '';
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