<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;


use common\extension\Code;
use Yii;
use common\models\forms\TemplateForm;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class TemplateController extends BaseController
{

    /**
     * 新增模板
     *
     * @SWG\POST(
     *     path="/template",
     *     operationId="createTemplate",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="新增模板(前台和后台通用)",
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
     *
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionCreate()
    {
        $model = new TemplateForm();
        if (!($result = $model->submit(Yii::$app->request->post()))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }

        return $result;
    }

    /**
     * 保存模板
     * @SWG\POST(
     *     path="/template/{templateId}",
     *     operationId="updateTemplate",
     *     schemes={"http"},
     *     tags={"模板接口"},
     *     summary="保存模板(前台和后台通用)",
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
     *        name="templateId",
     *        in="path",
     *        required=true,
     *        type="integer"
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
     *
     * @param $id
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionUpdate($id)
    {
        $model = new TemplateForm();
        $data = ArrayHelper::merge(Yii::$app->request->post(), ['template_id' => $id]);
        if (!($result = $model->submit($data))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }
        return $result;
    }

}