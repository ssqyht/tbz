<?php

namespace common\models\forms;

use common\models\Member;
use common\models\MyFavorite;
use common\models\TbzLetter;
use common\models\Team;
use common\models\TeamMember;
use common\models\TemplateOfficial;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;
use yii\helpers\Json;
use common\extension\Code;

class TeamForm extends Model
{
    use ModelErrorTrait;
    /** @var int 允许最大创建数量 */
    const MAX_TEAM_NUMBER = 5;
    /** @var int 创建者角色 */
    const CREATED_ROLE = 1;
    /** @var int 普通成员角色 */
    const MEMBER_ROLE = 4;
    public $team_name;
    public $founder_id;
    public $colors;
    public $fonts;
    public $team_mark;
    public $status;
    public $member_id;
    public $team_id;
    public $role;

    public function rules()
    {
        return [
            [['founder_id', 'status', 'team_id', 'member_id', 'role'], 'integer'],
            [['team_name'], 'string', 'max' => 100],
            [['team_mark'], 'string', 'max' => 200],
            [['colors', 'fonts'], 'validateColorsFonts'],
        ];
    }

    /**
     * 验证颜色和字体的格式是否为数组
     * @return bool
     */
    public function validateColorsFonts()
    {
        if (!$this->hasErrors()) {
            if (!is_array($this->colors) && $this->colors) {
                $this->addError('', '颜色必须是数组');
                return false;
            }
            if ($this->fonts && !is_array($this->colors)) {
                $this->addError('', '字体必须是数组');
                return false;
            }
        }
        return true;
    }

    /**
     * 创建团队
     * @return bool|Team
     */
    public function addTeam()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->team_name) {
            $this->addError('', '团队名称不能为空');
            return false;
        }
        if (!$this->user) {
            $this->addError('unlogin', '获取用户信息失败，请登录');
            return false;
        }
        if (!$this->isBeyondLimit()) {
            $this->addError('', '同一用户只能创建最多5个团队');
            return false;
        }
        if (!$this->team_mark) {
            //团队头像默认为创建者的头像
            $team_mark = Member::findIdentity($this->user);
            $this->team_mark = $team_mark->headimg_url;
        }
        $team_model = new Team();
        if ($team_model->load($this->attributes, '') && $team_model->save()) {
            //把创建者信息存入团队会员表
            $team_member_model = new TeamMember();
            $team_member_model->user_id = $this->user;
            $team_member_model->team_id = $team_model->id;
            $team_member_model->role = static::CREATED_ROLE;         //创建者角色
            $team_member_model->save();
            return $team_model;
        }
        return false;
    }

    /**
     * 编辑团队信息
     * @param $id
     * @return bool|Team|null
     */
    public function updateTeam($id)
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->user) {
            $this->addError('unlogin', '获取用户信息失败，请登录');
            return false;
        }
        $team_model = Team::findOne(['id' => $id]);
        if ($this->user != $team_model->founder_id) {
            $this->addError('unlogin', '非团队创建者无权对团队名称和头像进行修改');
            return false;
        }
        //修改团队名称
        if ($this->team_name) {
            $team_model->team_name = $this->team_name;
        }
        //修改团队头像
        if ($this->team_mark) {
            $team_model->team_mark = $this->team_mark;
        }
        //修改团队颜色
        if ($this->colors) {
            $team_model->colors = implode(',', $this->colors);
        }
        //修改团队字体
        if ($this->fonts) {
            $team_model->fonts = implode(',', $this->fonts);
        }
        if ($team_model->save()) {
            return $team_model;
        }
        return false;
    }

    /**
     * 删除团队
     * @param $id
     * @return bool
     */
    public function deleteTeam($id)
    {
        $team_model = Team::findOne(['id' => $id]);
        if ($this->user != $team_model->founder_id) {
            $this->addError('unlogin', '非团队创建者无权删除团队');
            return false;
        }
        $team_model->status = Team::RECYCLE_BIN_STATUS;
        if ($team_model->save(false)) {
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
        if ($this->founder_id === null) {
            $this->founder_id = 1/*\Yii::$app->user->id*/
            ;
        }
        return $this->founder_id;
    }

    /**
     * 判断是否超出最大创建团队的数量
     * @return bool
     */
    public function isBeyondLimit()
    {
        $number = $count = (new \yii\db\Query())
            ->from(Team::tableName())
            ->where(['founder_id' => $this->user, 'status' => Team::NORMAL_STATUS])
            ->count();
        if ($number >= static::MAX_TEAM_NUMBER) {
            return false;
        }
        return true;
    }

    public function getUserAuthority()
    {

    }
}