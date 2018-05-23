<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/17
 * Time: 15:57
 */

namespace common\models\forms;

use common\components\traits\ModelErrorTrait;
use common\models\FolderTemplateMember;
use common\models\FolderTemplateTeam;
use common\models\MaterialFolders;
use common\models\TeamMember;
use common\models\MaterialTeam;
use common\models\TemplateMember;
use common\models\Upfile;
use common\models\Folder;
use common\models\TemplateTeam;

class TemplateOperationForm extends \yii\base\Model
{
    use ModelErrorTrait;


    /** @var string 个人模板管理 */
    const TEMPLATE_MEMBER = 'template_member';
    /** @var string 团队模板管理 */
    const TEMPLATE_TEAM = 'template_team';


    /* @var integer  重命名 */
    const RENAME = 1;
    /* @var integer  移动到文件夹 */
    const MOVE_FOLDER = 2;
    /* @var integer  到回收站(初步删除) */
    const RECYCLE_BIN = 3;
    /* @var integer  删除 */
    const DELETE = 4;
    /* @var integer  还原 */
    const REDUCTION = 5;
    /* @var integer  个人转团队 */
    const PERSONAL_TRANSFER_TEAM = 6;


    /** @var int 到回收站 */
    const STATUS_TRASH = 7;
    /** @var int 删除 */
    const STATUS_DELETE = 3;
    /** @var int 还原 */
    const STATUS_NORMAL = 10;

    public $method;
    public $_table;
    public $_user;
    public $_condition;
    public $_folderModel;
    public $_tableModel;
    public $ids;
    public $name;
    public $folder;
    public $team_id;
    public $type;

    public function rules()
    {
        return [
            [['ids', 'type', 'method'], 'required'],
            [['folder', 'team_id'], 'integer'],
            ['name', 'string'],
            ['method', 'in', 'range' => [static::TEMPLATE_MEMBER, static::TEMPLATE_TEAM]],
        ];
    }

    /**
     * @param $params
     * @return bool|null
     * @throws \yii\db\Exception
     */
    public function operation($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }
        switch ($this->type) {
            case static::RENAME:
                return $this->rename();
            case static::MOVE_FOLDER:
                return $this->moveFolder();
            case static::RECYCLE_BIN:
                return $this->recycleBin();
            case static::DELETE :
                return $this->deleteTemplate();
            case static::REDUCTION :
                return $this->reduction();
            case static:: PERSONAL_TRANSFER_TEAM:
                return $this->transferTeam();
            default:
                return null;
        }
    }

    /**
     * 重命名
     * @return bool
     * @throws \yii\db\Exception
     */
    public function rename()
    {
        if (is_array($this->ids)) {
            $this->addError('', '不支持多个重命名');
            return false;
        }
        if (!$this->name) {
            $this->addError('', '重命名时文件名不能为空');
            return false;
        }
        return $this->batchProcessing('title', $this->name);
    }

    /**
     * 移动到文件夹
     * @return bool
     * @throws \yii\db\Exception
     */
    public function moveFolder()
    {
        if (!$this->folder) {
            $this->addError('', '移动到文件夹，文件夹id不能为空');
            return false;
        }
        if (!$this->isFolder()) {
            $this->addError('', '目标文件夹不存在');
            return false;
        }
        return $this->batchProcessing('folder_id', $this->folder);
    }

    /**
     * 到回收站
     * @return bool
     * @throws \yii\db\Exception
     */
    public function recycleBin()
    {
        return $this->batchProcessing('status', static::STATUS_TRASH);
    }

    /**
     * 删除
     * @return bool
     * @throws \yii\db\Exception
     */
    public function deleteTemplate()
    {
        return $this->batchProcessing('status', static::STATUS_DELETE);
    }

    /**
     * 还原
     * @return bool
     * @throws \yii\db\Exception
     */
    public function reduction()
    {
        return $this->batchProcessing('status', static::STATUS_NORMAL);
    }

    /**
     * 个人转团队
     * @return bool
     * @throws \yii\db\Exception
     */
    public function transferTeam()
    {
        if (!$this->team_id) {
            $this->addError('', '个人转团队，team_id不能为空');
            return false;
        }
        //查询将要复制的模板
        $template_data = TemplateMember::find()
            ->where(['template_id' => $this->ids])
            ->andWhere(['user_id' => $this->user])
            ->andWhere(['status' => TemplateMember::STATUS_NORMAL])
            ->all();
        $data = [];
        foreach ($template_data as $key => $value) {
            $data[] = [
                'classify_id' => $value->classify_id,
                'open_id' => $value->open_id,
                'user_id' => $value->user_id,
                'team_id' => $this->team_id,
                'folder_id' => $this->folder ? $this->folder : 0,
                'cooperation_id' => $value->cooperation_id,
                'title' => $value->title,
                'thumbnail_url' => $value->thumbnail_url,
                'thumbnail_id' => $value->thumbnail_id,
                'status' => 10,
                'is_diy' => $value->is_diy,
                'edit_from' => $value->edit_from,
                'amount_print' => $value->amount_print,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }
        $result = \Yii::$app->db->createCommand()->batchInsert(TemplateTeam::tableName(), ['classify_id', 'open_id', 'user_id', 'team_id', 'folder_id', 'cooperation_id', 'title', 'thumbnail_url', 'thumbnail_id', 'status', 'is_diy', 'edit_from', 'amount_print', 'created_at', 'updated_at'], $data)->execute();//执行批量添加
        return $result;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function batchProcessing($key, $value)
    {
        if ($this->table) {
            $result = \Yii::$app->db->createCommand()->update($this->_table, [$key => $value], $this->_condition)
                ->execute();
            if ($result) {
                //更新缓存
                \Yii::$app->dataCache->updateCache($this->_tableModel);
                return true;
            }
        }
        $this->addError('', '操作失败');
        return false;
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

    /**
     * 根据不同场景获取不同的文件名
     * @return array|bool
     */
    public function getTable()
    {
        if ($this->_table === null) {
            switch ($this->method) {
                case static::TEMPLATE_MEMBER:
                    //个人模板
                    $this->_table = TemplateMember::tableName();
                    $this->_condition = ['template_id'=>$this->ids,'user_id'=>$this->user];
                    $this->_tableModel = TemplateMember::class;
                    break;
                case static::TEMPLATE_TEAM:
                    //团队模板
                    if (!$this->isTeamMember()){
                        $this->_table = false;
                    }else{
                        $this->_table = TemplateTeam::tableName();
                        $this->_condition = ['template_id'=>$this->ids,'team_id'=>$this->team_id];
                        $this->_tableModel = TemplateTeam::class;
                    }
                    break;
                default:
                    $this->_table = false;
                    break;
            }
        }
        if ($this->_table) {
            return true;
        }
        return false;
    }

    /**
     * 判断文件夹是否存在
     * @param $folder_id
     * @return bool|Folder|null
     */
    public function isFolder()
    {
        if ($this->method == static::TEMPLATE_MEMBER) {
            $is_folder = FolderTemplateMember::findOne(['id' => $this->folder, 'user_id' => $this->user, 'status' => FolderTemplateMember::NORMAL_STATUS]);
        } elseif ($this->method == static::TEMPLATE_TEAM) {
            $is_folder = FolderTemplateTeam::findOne(['id' => $this->folder, 'team_id' => $this->user, 'status' => FolderTemplateTeam::NORMAL_STATUS]);
        } else {
            $is_folder = false;
        }
        return $is_folder;
    }

    /**
     * 验证当前用户是否是所要操作的团队成员
     * @return bool
     */
    public function isTeamMember(){
        $current_role = TeamMember::findOne(['user_id' => $this->user,'team_id'=>$this->team_id,'status'=>TeamMember::NORMAL_STATUS]);
        if (!$current_role) {
            $this->addError('','当前用户不属于所要操作的团队');
            return false;
        }
        return true;
    }
}