<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 13:30
 */

namespace common\models\forms;
use common\models\TeamMember;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;
class TeamMemberForm extends Model
{
    use ModelErrorTrait;
    /** @var int 创建者角色 */
    const CREATED_ROLE = 1;
    /** @var int 普通成员角色 */
    const MEMBER_ROLE = 4;
    public $status;
    public $member_id;
    public $team_id;
    public $role;
    private $_user;
    public function rules()
    {
        return [
            [['status', 'team_id', 'member_id', 'role'], 'integer'],
            [['member_id','team_id'],'required']
        ];
    }
    /**
     * 添加成员
     * @return bool|TeamMember
     */
    public function addMember()
    {
        if (!$this->validate()) {
            return false;
        }
       if (!$this->getUserAuthority()){
            $this->addError('','当前用户无权添加成员');
            return false;
       }
        $members = new TeamMember();
        $members->user_id = $this->member_id;
        $members->team_id = $this->team_id;
        //当不传角色值时，默认角色为普通成员角色
        if ($this->role) {
            $members->role = $this->role;
        } else {
            $members->role = static::MEMBER_ROLE;     //普通成员角色
        }
        if ($members->save()) {
            return $members;
        }
        $this->addError('', '添加成员失败');
        return false;
    }
    /**
     * 修改成员信息
     * @return bool|TeamMember|null
     */
    public function updateMember(){
        if (!$this->validate()) {
            return false;
        }
        if (!$this->getUserAuthority()){
            $this->addError('','当前用户无权修改成员信息');
            return false;
        }
        $member = TeamMember::findOne(['user_id'=>$this->member_id,'team_id'=>$this->team_id]);
        if (!$member){
            $this->addError('','成员不存在');
            return false;
        }
        if ($this->role){
            $member->role = $this->role;
        }
        if($member->save()){
            return $member;
        }
        $this->addError('','修改成员角色失败');
        return false;
    }

    /**
     * 删除成员
     * @return bool
     */
    public function deleteMember(){
        if (!$this->validate()){
            return false;
        }
        if (!$this->getUserAuthority()){
            $this->addError('','当前用户无权修改成员信息');
            return false;
        }
        $member = TeamMember::findOne(['user_id'=>$this->member_id,'team_id'=>$this->team_id]);
        if (!$member){
            $this->addError('','成员不存在');
            return false;
        }
        if ($member->delete()){
            return true;
        }
        return false;
    }
    /**
     * 验证当前用户的角色
     * @return bool
     */
    public function getUserAuthority()
    {
        $current_role = TeamMember::findOne(['user_id' => $this->user]);
        if ($current_role->role != 1) {
            return false;
        }
        return true;
    }
    /**
     * @return int 获取用户信息
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = 1/*\Yii::$app->user->id*/
            ;
        }
        return $this->_user;
    }
}