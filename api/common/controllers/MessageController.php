<?php
/**
 * Created by PhpStorm.
 * User: swz
 * Date: 2018/5/11
 * Time: 11:34
 */

namespace api\common\controllers;

use common\models\TbzLetter;
use common\models\search\MessageSearch;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use common\models\forms\MessageForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class MessageController extends  RestController
{
    /**
     * @SWG\Get(
     *     path="/message",
     *     operationId="getMessage",
     *     schemes={"http"},
     *     tags={"消息接口"},
     *     summary="根据状态获取消息",
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
     *          description="消息状态",
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="消息类型",
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
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $config = [
            'scenario' => \Yii::$app->request->isFrontend() ? MessageSearch::SCENARIO_FRONTEND : MessageSearch::SCENARIO_BACKEND
        ];
        $message = new MessageSearch($config);
        $result = $message->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/message",
     *     operationId="addMessage",
     *     schemes={"http"},
     *     tags={"消息接口"},
     *     summary="添加新消息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="title",
     *          type="string",
     *          description="消息标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="subtitle",
     *          type="string",
     *          description="副标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="description",
     *          type="string",
     *          description="消息描述",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="消息类型",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_id",
     *          type="string",
     *          description="用户id(消息类型为个人消息时必填)",
     *     ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否发布（1为待发布，2为已发布)",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序逆序",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TbzLetter"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $create_data = \Yii::$app->request->post();
        $message = new MessageForm();
        if ($message->load($create_data, '') && ($result = $message->addMessage())) {
            return $result;
        }
        throw new BadRequestHttpException($message->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/message/{id}",
     *     operationId="updateMessage",
     *     schemes={"http"},
     *     tags={"消息接口"},
     *     summary="修改消息",
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
     *          description="消息id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="title",
     *          type="string",
     *          description="消息标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="subtitle",
     *          type="string",
     *          description="副标题",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="description",
     *          type="string",
     *          description="消息描述",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="消息类型",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="user_id",
     *          type="string",
     *          description="用户id(消息类型为个人消息时必填)",
     *     ),
     *       @SWG\Parameter(
     *          in="formData",
     *          name="status",
     *          type="integer",
     *          description="是否发布（1为待发布，2为已发布)",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="排序逆序",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TbzLetter"
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
     * @return bool|TbzLetter|null
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $update_data = \Yii::$app->request->post();
        $message = new MessageForm();
        if ($message->load($update_data, '') && ($result = $message->updateMessage($id))) {
            return $result;
        }
        throw new BadRequestHttpException($message->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/message/{id}",
     *     operationId="deleteMessage",
     *     schemes={"http"},
     *     tags={"消息接口"},
     *     summary="删除消息",
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
     *          description="消息id",
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
     * @throws HttpException
     */
    public function actionDelete($id)
    {
        $message = new MessageForm();
        if ($result = $message->deleteMessage($id)) {
            return $result;
        }
        throw new HttpException(500, $message->getStringErrors(), Code::SERVER_FAILED);
    }
}