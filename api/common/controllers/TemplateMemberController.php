<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\Folder;
use common\models\forms\TemplateForm;
use common\models\search\TemplateMemberSearch;
use common\models\TbzLetter;
use common\models\search\MessageSearch;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use common\models\forms\MessageForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
class TemplateMemberController extends BaseController
{

    /**
     * @SWG\Get(
     *     path="/Template-member",
     *     operationId="getTemplateMember",
     *     schemes={"http"},
     *     tags={"个人模板接口"},
     *     summary="根据条件查询模板信息",
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
     *          description="模板状态",
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="folder",
     *          type="integer",
     *          description="所在文件夹的id",
     *     ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="folder",
     *          type="integer",
     *          description="所在文件夹的id",
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="product",
     *          type="integer",
     *          description="小分类",
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TbzLetter")
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
        $template_member = new TemplateMemberSearch();
        $result = $template_member->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }
    /**
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $model = new TemplateForm();
        $data = ArrayHelper::merge(\Yii::$app->request->post(), ['method' => TemplateForm::METHOD_SAVE_MEMBER ]);
        if (!($result = $model->submit($data))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }

        return $result;
    }

    public function actionUpdate()
    {

    }

    public function actionDelete()
    {

    }

}