<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/19
 * Time: 10:06
 */

namespace common\models\forms;

use common\models\MyFavoriteMember;
use common\models\MyFavoriteTeam;
use common\models\TemplateOfficial;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;
class MyFavoriteForm extends Model
{
    use ModelErrorTrait;

    /** @var string 个人收藏 */
    const FAVORITE_MEMBER = 'favorite_member';
    /** @var string 团队收藏 */
    const FAVORITE_TEAM = 'favorite_team';

    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;
    /** @var int 删除状态 */
    const DELETE_STATUS = 3;



    public $template_id;
    public $user_id;
    public $team_id;
    public $method;

    public function rules()
    {
        return [
            [['template_id'], 'required'],
            [['template_id','team_id'], 'integer'],
            ['method','string'],
            ['method', 'in', 'range' => [static::FAVORITE_MEMBER, static::FAVORITE_TEAM]],
        ];
    }

    /** 添加收藏
     * @return bool
     * @throws \yii\db\Exception
     */
    public function addMyFavorite()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->user) {
            $this->addError('unlogin', '获取用户信息失败，请登录');
            return false;
        }
        $template_model = TemplateOfficial::findOne(['template_id' => $this->template_id]);
        if (!$template_model){
            $this->addError('', '收藏的模板不存在');
            return false;
        }
        if ($this->method == static::FAVORITE_TEAM){
            //团队
            if (MyFavoriteTeam::findOne(['template_id'=>$this->template_id,'team_id'=>$this->team_id])){
                $this->addError('', '已收藏，不用重复收藏');
                return false;
            }
            $model = new MyFavoriteTeam();
            $model->team_id = $this->team_id;
            $model->user_id = $this->user;
            $model->template_id = $this->template_id;
        }else{
            //个人
            if (MyFavoriteMember::findOne(['template_id'=>$this->template_id,'user_id'=>$this->user])){
                $this->addError('', '已收藏，不用重复收藏');
                return false;
            }
            $model = new MyFavoriteMember();
            $model->user_id = $this->user;
            $model->template_id = $this->template_id;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //保存收藏
            $model->save();
            //增加模板的收藏量
            $template_model->updateCounters(['amount_favorite' => 1]);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', '收藏失败');
            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('', '收藏失败');
            return false;
        }
        return true;
    }

    /**
     * 删除收藏
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteMyFavorite($id)
    {
        if ($this->method == static::FAVORITE_TEAM){
            //团队
            $model = MyFavoriteTeam::findOne(['id'=>$id,'team_id'=>$this->team_id]);
        }else{
            //个人
            $model= MyFavoriteMember::findOne(['id'=>$id,'user_id'=>$this->user]);
        }
        if (!$model) {
            $this->addError('id', '该收藏不存在');
        }
       // $model->status = static::RECYCLE_BIN_STATUS;
        if ($model->delete()) {
            return true;
        }
        $this->addError('', '删除失败');
        return false;
    }
    /**
     * @return int 获取用户信息
     */
    public function getUser()
    {
        if ($this->user_id === null) {
            $this->user_id = 1/*\Yii::$app->user->id*/
            ;
        }
        return $this->user_id;
    }
}