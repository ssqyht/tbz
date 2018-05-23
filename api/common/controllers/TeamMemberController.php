<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 13:27
 */
namespace api\common\controllers;

use common\models\forms\MyFavoriteForm;
use common\models\forms\TeamMemberForm;
use common\models\MyFavorite;
use common\models\TbzLetter;
use common\models\search\TeamMemberSearch;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use common\components\vendor\RestController;
use common\models\forms\MessageForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
class TeamMemberController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/team-member",
     *     operationId="getTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="获取成员信息",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="query",
     *          name="team_id",
     *          type="integer",
     *          description="团队的唯一标识team_id",
     *          required = true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TeamMember")
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
    public function actionIndex(){
        $model = new TeamMemberSearch();
        $result = $model->search(\Yii::$app->request->get());
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/team-member",
     *     operationId="addTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="添加团队成员",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="team_id",
     *          type="integer",
     *          description="团队的唯一标识team_id",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="member_id",
     *          type="integer",
     *          description="成员的唯一标识member_id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="role",
     *          type="integer",
     *          description="成员的角色,默认为普通会员角色，1创建者，2管理员，3设计师，4普通成员",
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TeamMember"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|\common\models\TeamMember
     * @throws BadRequestHttpException
     * 添加成员
     */
    public function actionCreate(){
        $create_data = \Yii::$app->request->post();
        $model = new TeamMemberForm();
        if ($model->load($create_data, '') && ($result = $model->addMember())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/team-member/{id}",
     *     operationId="updateTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="编辑团队成员",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="team_id",
     *          type="integer",
     *          description="团队的唯一标识team_id",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="member_id",
     *          type="integer",
     *          description="成员的唯一标识member_id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="role",
     *          type="integer",
     *          description="成员的角色,默认为普通会员角色，1创建者，2管理员，3设计师，4普通成员",
     *          required=true,
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Team"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool|\common\models\TeamMember|null
     * @throws BadRequestHttpException
     * 修改成员信息
     */
    public function actionUpdate(){
        $update_data = \Yii::$app->request->post();
        $model = new TeamMemberForm();
        if ($model->load($update_data, '') && ($result = $model->updateMember())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/team-member/{id}",
     *     operationId="deleteTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="删除团队成员",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="team_id",
     *          type="integer",
     *          description="团队的唯一标识team_id",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
     *          in="formData",
     *          name="member_id",
     *          type="integer",
     *          description="成员的唯一标识member_id",
     *          required=true,
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return bool
     * @throws HttpException
     * 删除成员
     */
    public function actionDelete(){
        $model = new TeamMemberForm();
        $data = \Yii::$app->request->post();
        if ($model->load($data, '') && $model->deleteMember()) {
            return true;
        }
        throw new HttpException(500, $model->getStringErrors(), Code::SERVER_FAILED);
    }
}