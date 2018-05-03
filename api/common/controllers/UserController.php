<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use common\models\forms\LoginForm;
use common\models\Member;
use yii\rest\Controller;

class UserController extends Controller
{

    /**
     * @SWG\Get(path="/user/login",
     *     tags={"user"},
     *     summary="手机号登录接口",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "mobile",
     *        description = "手机号",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "password",
     *        description = "密码",
     *        required = true,
     *        type = "string"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "success",
     *         @SWG\Schema(
     *           ref="$/definitions/Response",
     *         )
     *     )
     * )
     *
     */
    public function actionLogin()
    {
    }

    public function actionIndex()
    {
    }

}