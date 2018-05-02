<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use yii\rest\Controller;

class UserController extends Controller
{

    /**
     * @SWG\Get(path="/user/login",
     *     tags={"user"},
     *     summary="用户登录",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "query",
     *        name = "access_token",
     *        description = "access token",
     *        required = true,
     *        type = "string"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
    public function actionLogin()
    {

    }

}