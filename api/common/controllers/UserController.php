<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\Member;
use Yii;
use common\components\vendor\RestController;
use common\models\forms\RegisterForm;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class UserController extends RestController
{

    /**
     * 用户账号密码登录
     * @author thanatos <thanatos915@163.com>
     */
    public function actionLogin()
    {

    }

    /**
     * 绑定手机号
     * @SWG\Post(
     *     path="/user/bind",
     *     tags={"用户相关接口"},
     *     summary="绑定手机号",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile",
     *         description="手机号",
     *         in="formData",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="code",
     *         description="手机验证码",
     *         in="formData",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         description="密码",
     *         in="formData",
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
     *                  ref="#/definitions/Member"
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
     * @return Member
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @author thanatos <thanatos915@163.com>
     */
    public function actionBind()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
        $model = new RegisterForm(['scenario' => RegisterForm::SCENARIO_BIND]);
        if ($model->load(Yii::$app->request->post(), '') && ($result = $model->bind())) {
            return Yii::$app->user->identity;
        } else {
            throw new BadRequestHttpException($model->getStringErrors());
        }
    }





}