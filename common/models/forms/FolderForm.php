<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/16
 * Time: 20:08
 */

namespace common\models\forms;

use common\models\Folder;
use common\models\TemplateMember;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;
use common\models\MaterialFolders;

class FolderForm extends Model
{
    use ModelErrorTrait;
    /** @var string 模板文件夹 */
    const TEMPLATE_FOLDER = 'template_folder';
    /** @var string 素材文件夹 */
    const MATERIAL_FOLDER = 'material_folder';
    /* @var integer 正常状态 */
    const STATUS_NORMAL = '10';
    /* @var integer 假删除 */
    const FALSE_DELETE = '7';
    /* @var integer 真删除 */
    const REALLY_DELETE = '3';
    /* @var integer 默认文件夹 */
    const DEFAULT_FOLDER = '0';
    public $_tableModel;
    public $name;
    public $color;
    public $user_id;
    public $method;
    public function rules()
    {
        return [
            [['method','name','color'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 200],
            ['method', 'in', 'range' => [static::TEMPLATE_FOLDER, static::MATERIAL_FOLDER]],
        ];
    }
    /** @var array 相关表 */
    public $relation_table = [
        self::TEMPLATE_FOLDER => 'tu_template_member',
        self::MATERIAL_FOLDER => 'tu_upfile',
    ];

    /**
     * @return bool|Folder
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
     * @return bool|Folder|null
     */
    public function updateFolder($id)
    {
        //验证信息
        if(!$this->validateData()){
            return false;
        }
        $folder = ($this->tableModel)::findOne(['id'=>$id,'user_id'=>$this->user]);
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
        $folder = ($this->tableModel)::findOne(['id'=>$id,'user_id'=>$this->user]);
        if (!$folder) {
            $this->addError('id', '该文件夹不存在');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            \Yii::$app->db->createCommand()->update($this->relation_table[$this->method], ['folder_id' => static::DEFAULT_FOLDER], ['folder_id' => $id, 'user_id' => $this->user])->execute();
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
                case static::TEMPLATE_FOLDER:
                    $this->_tableModel = Folder::class;
                    break;
                case static::MATERIAL_FOLDER:
                    $this->_tableModel = MaterialFolders::class;
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
