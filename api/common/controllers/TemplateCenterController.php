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
use Yii;
use yii\web\NotFoundHttpException;
use common\extension\Code;
class TemplateCenterController extends RestController
{
        /**
     * @SWG\Get(
     *     path="/template-center/classify-search",
     *     operationId="classifySearch",
     *     schemes={"http"},
     *     tags={"模板中心接口"},
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
        $config = [
            'scenario' => $this->isFrontend() ? TemplateCenterSearch::SCENARIO_FRONTEND : TemplateCenterSearch::SCENARIO_BACKEND
        ];
        $model = new TemplateCenterSearch($config);
        $result = $model->search();
        if(!$result){
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        return $result;
    }
    public function actionSearch(){
        $model = new TemplateCenterSearch();
        $condition = Yii::$app->request->get();
        $result = $model->conditionSearch($condition);
        return $result;
    }
}