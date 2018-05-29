<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 13:27
 */
namespace api\common\controllers;

use Yii;
use common\models\forms\TeamMemberForm;
use common\models\search\TeamMemberSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\extension\Code;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
class TeamMemberController extends BaseController
{
    /**
     * @SWG\Get(
     *     path="/team-member",
     *     operationId="getTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="获取成员信息",
     *     description="此接口用来获取当前团队下的所有成员信息,前端调用",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台",
     *     ),
     *     @SWG\Parameter(
     *         name="Team",
     *         in="header",
     *         type="integer",
     *         required=true,
     *         description="团队的唯一标识team_id",
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
//        $result = $model->search(['team_id'=>\Yii::$app->request->getTeam()]);
        $result = $model->search(['team_id'=>Yii::$app->request->getTeam()->id]);
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
     *     description="此接口用来新增当前团队下的成员,前端调用，成功返回新添加的成员信息",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台",
     *     ),
     *     @SWG\Parameter(
     *         name="Team",
     *         in="header",
     *         type="integer",
     *         required=true,
     *         description="团队的唯一标识team_id",
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
     *          description="成员的角色,默认为普通成员角色，1创建者，2管理员，3设计师，4普通成员",
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
        $create_data= ArrayHelper::merge(\Yii::$app->request->post(),['team_id'=>\Yii::$app->request->getTeam()]);
        $model = new TeamMemberForm();
        if ($model->load($create_data, '') && ($result = $model->addMember())) {
            return $result;
        }
        throw new BadRequestHttpException($model->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Put(
     *     path="/team-member/{member_id}",
     *     operationId="updateTeamMember",
     *     schemes={"http"},
     *     tags={"团队接口"},
     *     summary="编辑团队成员",
     *     description="此接口用来编辑成员的角色信息，成功返回编辑后的成员信息",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台",
     *     ),
     *     @SWG\Parameter(
     *         name="Team",
     *         in="header",
     *         type="integer",
     *         required=true,
     *         description="团队的唯一标识team_id",
     *     ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="member_id",
     *          type="integer",
     *          description="成员的唯一标识member_id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="role",
     *          type="integer",
     *          description="1创建者，2管理员，3设计师，4普通成员",
     *          required=true,
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
     * @return bool|\common\models\TeamMember|null
     * @throws BadRequestHttpException
     * 修改成员信息
     */
    public function actionUpdate($id){
        $update_data = ArrayHelper::merge(\Yii::$app->request->post(),['team_id'=>\Yii::$app->request->getTeam(),'member_id'=>$id]);
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
     *    description="此接口用来删除当前团队下的某一用户，成功返回空字符串",
     *     @SWG\Parameter(
     *         name="Client",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="公共参数",
     *     ),
     *     @SWG\Parameter(
     *         name="Handle",
     *         in="header",
     *         type="string",
     *         description="公共参数,区分前后台，frontend为前台,backend为后台,默认为前台",
     *     ),
     *     @SWG\Parameter(
     *         name="Team",
     *         in="header",
     *         type="integer",
     *         required=true,
     *         description="团队的唯一标识team_id",
     *     ),
     *      @SWG\Parameter(
     *          in="path",
     *          name="id",
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
    public function actionDelete($id){
        $model = new TeamMemberForm();
        $data = ['team_id'=>\Yii::$app->request->getTeam(),'member_id'=>$id];
        if ($model->load($data, '') && $model->deleteMember()) {
            return '';
        }
        throw new HttpException(500, $model->getStringErrors(), Code::SERVER_FAILED);
    }
}