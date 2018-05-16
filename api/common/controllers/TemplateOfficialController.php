<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/14
 * Time: 13:46
 */

namespace api\common\controllers;

use common\components\vendor\RestController;
use common\models\search\TemplateCenterSearch;
use common\models\search\TemplateOfficialSearch;
use common\models\TemplateOfficial;
use Yii;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\rest\Controller;

class TemplateOfficialController extends RestController
{

    /**
     * @SWG\Get(
     *     path="/template-official/classify-search",
     *     operationId="classifySearch",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="模板中心首页根据分类展示模板信息",
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
     *                      @SWG\Items(ref="#/definitions/TemplateOfficial")
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
     * @return \common\models\Category[]|null
     * @throws NotFoundHttpException
     */
    public function actionClassifySearch(){
        $model = new TemplateCenterSearch();
        $result = $model->search();
        if(!$result){
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @SWG\Get(
     *     path="/template-official",
     *     operationId="getTemplate",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="官方模板查询接口",
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
     *         in="formData",
     *         name="classify_id",
     *         type="integer",
     *         description="模板分类",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         in="formData",
     *         name="price",
     *         type="integer",
     *         description="模板价格,1-4对应不同价格区间",
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="tag_style_id",
     *         type="integer",
     *         description="风格tag_id",
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="tag_industry_id",
     *         type="integer",
     *         description="行业tag_id",
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="sort",
     *         type="integer",
     *         description="按热度排序",
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
     *                      @SWG\Items(ref="#/definitions/TemplateOfficial")
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
     * @return array|bool|null|\yii\db\ActiveQuery
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $config = [
            'scenario' => $this->isFrontend() ? TemplateOfficialSearch::SCENARIO_FRONTEND : TemplateOfficialSearch::SCENARIO_BACKEND
        ];
        $model = new TemplateOfficialSearch($config);
        $result = $model->search(Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/template-official",
     *     operationId="addOfficial",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="添加官方模板",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TemplateOfficial")
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TemplateOfficial"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return TemplateOfficial
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $create_data = \Yii::$app->request->post();
        $model = new TemplateOfficial();
        if ($model->load($create_data, '') && ($model->save())) {
            return $model;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/template-official/{id}}",
     *     operationId="updateOfficial",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="修改官方模板",
     *     @SWG\Parameter(
     *          in="path",
     *          name="id",
     *          type="integer",
     *          description="唯一id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TemplateOfficial")
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TemplateOfficial"
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
     * @return TemplateOfficial|null
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $update_data = \Yii::$app->request->post();
        $model = TemplateOfficial::findOne(['template_id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        if ($model->load($update_data, '') && $model->save()) {
            return $model;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/template-official/{id}",
     *     operationId="deleteOfficial",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="删除模板",
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
     *          description="唯一id",
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
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = TemplateOfficial::findOne(['template_id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        $model->status = 5;
        if ($model->save()) {
            return true;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_FAILED);
    }
}