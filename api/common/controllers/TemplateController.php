<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;


use common\extension\Code;
use Yii;
use common\models\forms\TemplateForm;
use yii\web\BadRequestHttpException;

class TemplateController extends BaseController
{

    /**
     * @return bool|\common\models\TemplateMember|\common\models\TemplateOfficial
     * @throws BadRequestHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionCreate()
    {
        $config = [
            'scenario' => $this->isFrontend() ? TemplateForm::SCENARIO_FRONTEND : TemplateForm::SCENARIO_BACKEND
        ];
        $model = new TemplateForm($config);
        if (!($result = $model->submit(Yii::$app->request->post()))) {
            throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
        }

        var_dump($result);exit;
        return $result;

    }

}