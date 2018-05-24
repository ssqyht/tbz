<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/16
 * Time: 20:08
 */

namespace common\models\forms;

use common\models\TemplateTeam;
use common\models\FolderTemplateMember;
use common\models\FolderTemplateTeam;
use common\models\TemplateMember;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;

class FolderTemplateForm extends Model
{
    use ModelErrorTrait;
    /** @var string 个人模板文件夹 */
    const FOLDER_TEMPLATE_MEMBER = 'folder_template_member';
    /** @var string 团队模板文件夹 */
    const FOLDER_TEMPLATE_TEAM  = 'folder_template_team';


    /* @var integer 正常状态 */
    const STATUS_NORMAL = '10';
    /* @var integer 假删除 */
    const FALSE_DELETE = '7';
    /* @var integer 真删除 */
    const REALLY_DELETE = '3';
    /* @var integer 默认文件夹 */
    const DEFAULT_FOLDER = '0';

    public $name;
    public $color;
    public $user_id;
    public $method;
    public $team_id;

    private $_tableModel;
    private $_condition;
    private $_cacheModel;

    public function rules()
    {
        return [
            [['method','name','color'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 200],
            ['team_id','integer'],
            ['method', 'in', 'range' => [static::FOLDER_TEMPLATE_MEMBER, static::FOLDER_TEMPLATE_TEAM]],
        ];
    }
    /** @var array 相关表 */
    public $relation_table = [
        self::FOLDER_TEMPLATE_MEMBER => 'tu_template_member', //个人模板
        self::FOLDER_TEMPLATE_TEAM => 'tu_template_team',      //团队模板
    ];

    /**
     * @return bool
     * 添加新文件夹
     */
    public function addFolder()
    {
        //验证信息
        if(!$this->validateData()){
            return false;
        }
        $folder = new $this->tableModel;
        if ($folder->load($this->attributes, '') && $folder->save()) {
            return $folder;
        }
        return false;
    }

    /**
     * 编辑文件夹
     * @param $id
     * @return bool|null
     */
    public function updateFolder($id)
    {
        //验证信息
        if(!$this->validateData()){
            return false;
        }
        $folder = ($this->tableModel)::find()->where(['id'=>$id])->andWhere($this->_condition)->one();
        if (!$folder) {
            $this->addError('', '该文件夹不存在');
            return false;
        }
        if ($folder->load($this->attributes, '') && $folder->save()) {
            return $folder;
        }
        $this->addError('', '修改失败');
        return false;
    }

    /**
     * 删除文件夹
     * @param $id
     * @return bool
     */
    public function deleteFolder($id)
    {
        //验证信息
        if (!$this->tableModel) {
            return false;
        }
        if (!$this->user) {
            $this->addError('noLogin', '获取用户信息失败，请登录');
            return false;
        }
        $folder = ($this->tableModel)::find()->where(['id'=>$id])->andWhere($this->_condition)->one();
        if (!$folder) {
            $this->addError('id', '该文件夹不存在');
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->_condition = array_merge($this->_condition,['folder_id' => $id]);
            \Yii::$app->db->createCommand()->update($this->relation_table[$this->method], ['folder_id' => static::DEFAULT_FOLDER], $this->_condition)->execute();
            $folder->status = static::FALSE_DELETE;
            $folder->save(false);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('', '删除失败');
            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('', '删除失败');
            return false;
        }
        //更新缓存
        \Yii::$app->dataCache->updateCache($this->_cacheModel);
        return true;
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

    /**
     * 获取模型
     * @return bool|string
     */
    public function getTableModel()
    {
        if ($this->_tableModel === null) {
            switch ($this->method) {
                case static::FOLDER_TEMPLATE_MEMBER:
                    //个人模板文件夹
                    $this->_tableModel = FolderTemplateMember::class;
                    $this->_condition = ['user_id'=>$this->user];
                    $this->_cacheModel = TemplateMember::class;
                    break;
                case static::FOLDER_TEMPLATE_TEAM:
                    //团队模板文件夹
                    $this->_tableModel = FolderTemplateTeam::class;
                    $this->_condition = ['team_id'=>$this->team_id];
                    $this->_cacheModel = TemplateTeam::class;
                    break;
                default:
                    $this->_tableModel = false;
                    break;
            }
        }
        return $this->_tableModel;
    }
    /**
     * 验证信息
     * @return bool
     */
    public function validateData(){
        if (!$this->validate()) {
            return false;
        }
        if (!$this->tableModel) {
            return false;
        }
        if (!$this->user) {
            $this->addError('noLogin', '获取用户信息失败，请登录');
            return false;
        }
        return true;
    }
}
