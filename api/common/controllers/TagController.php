<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/11
 * Time: 17:15
 */

namespace api\common\controllers;

use common\components\vendor\RestController;
use common\models\search\TagSearch;
use common\models\Tag;
use yii\web\NotFoundHttpException;
use yii\rest\Controller;
use common\extension\Code;
use yii\web\BadRequestHttpException;
class TagController extends RestController
{
    /**
     * @SWG\Get(
     *     path="/tag",
     *     operationId="getTag",
     *     schemes={"http"},
     *     tags={"分类相关接口"},
     *     summary="获取Tag信息",
     *     @SWG\Parameter(
     *         name="client",
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
     *                  @SWG\Items(ref="#/definitions/Tag")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return $result
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $tag_search = new TagSearch();
        if ($result = $tag_search->search()) {
            return $result;
        }
        throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
    }

    /**
     * @SWG\Post(
     *     path="/tag",
     *     operationId="addTag",
     *     schemes={"http"},
     *     tags={"分类相关接口"},
     *     summary="添加新Tag",
     *     @SWG\Parameter(
     *         name="client",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="标签名",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="标签类型",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="sort",
     *          type="integer",
     *          description="热度",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="请求成功",
     *          ref="$/responses/Success",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Tag"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="请求失败",
     *          ref="$/responses/Error",
     *     ),
     * )
     * @return tag
     * @throws BadRequestHttpException
     */
    public function actionCreate(){
        $create_data = \Yii::$app->request->post();
        $tag = new Tag();
        if ($tag->load($create_data,'') && ($tag->save())){
            return $tag;
        }
        throw new BadRequestHttpException($tag->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }
    /**
     * @SWG\Put(
     *     path="/tag/{id}",
     *     operationId="updateTag",
     *     schemes={"http"},
     *     tags={"分类相关接口"},
     *     summary="修改tag",
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
     *          description="tag的id",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="name",
     *          type="string",
     *          description="tag名称",
     *          required=true,
     *     ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="type",
     *          type="integer",
     *          description="tag类型(0为已弃用，1为风格，2为行业)",
     *          required=true,
     *     ),
     *      @SWG\Parameter(
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
     *                  ref="#/definitions/Tag"
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
     * @return Tag|null
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id){
        $update_data = \Yii::$app->request->post();
        $tag = Tag::findOne($id);
        if (!$tag){
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        if ($tag->load($update_data,'') && $tag->save()){
            return $tag;
        }
        throw new BadRequestHttpException($tag->getStringErrors(), Code::SERVER_UNAUTHORIZED);
    }

    /**
     * @SWG\Delete(
     *     path="/tag/{id}",
     *     operationId="deleteTag",
     *     schemes={"http"},
     *     tags={"分类相关接口"},
     *     summary="删除tag",
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
     *          description="tag的id",
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
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id){
        $tag = Tag::findOne($id);
        if (!$id){
            throw new NotFoundHttpException('', Code::SOURCE_NOT_FOUND);
        }
        $tag->type = 0;
        if ($tag->save()){
            return true;
        }
        throw new BadRequestHttpException($tag->getStringErrors(), Code::SERVER_FAILED);
    }

    public function actionTagSearch(){
        $search_data = \Yii::$app->request->get();
        $tag_search = new TagSearch();
        if ($result = $tag_search->queryIndex($search_data)) {
            return $result;
        }
        return $result;
    }
}