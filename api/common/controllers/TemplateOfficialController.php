<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/14
 * Time: 13:46
 */

namespace api\common\controllers;

use common\components\vendor\RestController;
use common\models\forms\TemplateForm;
use common\models\search\TemplateCenterSearch;
use common\models\search\TemplateOfficialSearch;
use common\models\TemplateOfficial;
use Yii;
use yii\helpers\ArrayHelper;
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
    public function actionClassifySearch()
    {
        $model = new TemplateCenterSearch();
        $result = $model->search();
        if (!$result) {
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @SWG\Get(
     *     path="/template-official",
     *     operationId="getTemplate",
     *     schemes={"http"},
     *     tags={"官方模板接口"},
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
     *         name="product",
     *         type="integer",
     *         description="模板分类",
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
     * 查询官方模板数据
     * @param integer $id
     * @return array|TemplateOfficial|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionView($id)
    {
        $model = TemplateOfficial::findById($id);
        if (empty($model)) {
            throw new NotFoundHttpException('资源不存在', Code::SOURCE_NOT_FOUND);
        }
        return $model;
    }



}