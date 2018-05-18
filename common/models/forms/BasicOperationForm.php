<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/17
 * Time: 15:57
 */

namespace common\models\forms;

use common\components\traits\ModelErrorTrait;
use common\models\MaterialFolders;
use common\models\TemplateMember;
use common\models\Upfile;
use common\models\Folder;
class BasicOperationForm extends \yii\base\Model
{
    use ModelErrorTrait;
    /** @var string 素材管理 */
    const UPFILE = 'upfile';
    /** @var string 个人模板管理 */
    const TEMPLATE_MEMBER = 'template_member';
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
    /* @var integer  还原 */
    const PERSONAL_TRANSFER_TEAM = 6;
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
            [['ids', 'type','method'], 'required'],
            [['folder', 'team_id'], 'integer'],
            ['name', 'string'],
            ['method', 'in', 'range' => [static::UPFILE, static::TEMPLATE_MEMBER]],
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
        if (!$this->isFolder()){
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
        return $this->batchProcessing('status', TemplateMember::STATUS_TRASH);
    }

    /**
     * 删除
     * @return bool
     * @throws \yii\db\Exception
     */
    public function deleteTemplate()
    {
        return $this->batchProcessing('status', TemplateMember::STATUS_DELETE);
    }

    /**
     * 还原
     * @return bool
     * @throws \yii\db\Exception
     */
    public function reduction()
    {
        return $this->batchProcessing('status', TemplateMember::STATUS_NORMAL);
    }

    /**
     * 个人转团队
     * @return bool
     * @throws \yii\db\Exception
     */
    public function transferTeam(){
        if (!$this->team_id) {
            $this->addError('', '个人转团队，team_id不能为空');
            return false;
        }
        return $this->batchProcessing('team_id',$this->team_id);
    }
    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function batchProcessing($key, $value)
    {
        if ($this->table){
            $result = \Yii::$app->db->createCommand()->update($this->_table, [$key => $value], [ $this->_condition =>$this->ids, 'user_id' => $this->user,'team_id'=>0])
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
    public function getTable(){
        if ($this->_table === null){
            switch ($this->method){
                case static::UPFILE:
                    $this->_table = Upfile::tableName();
                    $this->_condition = 'id';
                    $this->_tableModel = Upfile::class;
                    break;
                case static::TEMPLATE_MEMBER:
                    $this->_table = TemplateMember::tableName();
                    $this->_condition = 'template_id';
                    $this->_tableModel = TemplateMember::class;
                    break;
                default:
                    $this->_table = false;
                    break;
            }
        }
        if ($this->_table){
            return true;
        }
        return false;
    }

    /**
     * 判断文件夹是否存在
     * @param $folder_id
     * @return bool|Folder|null
     */
    public function isFolder(){
        if ($this->method == static::UPFILE){
            $is_folder = Folder::findOne(['id'=>$this->folder,'user_id'=>$this->user,'status'=>Folder::STATUS_ONLINE]);
        }elseif ($this->method == static::TEMPLATE_MEMBER){
            $is_folder = MaterialFolders::findOne(['id'=>$this->folder,'user_id'=>$this->user,'status'=>Folder::STATUS_ONLINE]);
        }else{
            $is_folder = false;
        }
       return $is_folder;
    }
}