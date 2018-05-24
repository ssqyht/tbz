<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/22
 * Time: 10:42
 */
namespace common\models\search;

use common\components\vendor\Model;
use common\models\FolderMaterialMember;
use common\models\FolderMaterialTeam;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;

class FolderMaterialSearch extends Model
{
    /** @var string 个人素材文件夹 */
    const MATERIAL_FOLDER_MEMBER = 'material_folder_member';
    /** @var string 团队素材文件夹 */
    const MATERIAL_FOLDER_TEAM = 'material_folder_team';


    public $status;
    public $method;
    public $team_id;

    public $_user;
    private $_cacheKey;
    private $_tableModel;
    private $_condition;


    public function rules()
    {
        return [
            [['status','team_id'], 'integer'],
            ['method', 'required'],
            ['method', 'in', 'range' => [static::MATERIAL_FOLDER_MEMBER, static::MATERIAL_FOLDER_TEAM]],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['method','team_id'],
            static::SCENARIO_BACKEND => ['status', 'method','team_id'],
            static::SCENARIO_FRONTEND => ['method','team_id']
        ];
    }

    /**
     * @param $params
     * @return array|mixed|null
     */
    public function search($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }
        switch ($this->scenario) {
            case static::SCENARIO_FRONTEND:
                return $this->searchFrontend();
            case static::SCENARIO_BACKEND:
            case static::SCENARIO_DEFAULT:
                return $this->searchBackend();
            default:
                return null;
        }
    }

    /**
     * @return mixed|null 前台获取文件夹信息
     */
    public function searchFrontend()
    {
        if (!$this->tableModel) {
            return false;
        }
        $folder = ($this->tableModel)::online()
            ->andWhere($this->_condition);
        // 查询数据 使用缓存
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($folder) {
                $result_data = $folder->all();
                return $result_data;
            }, $this->cacheKey, CacheDependency::FOLDER_MATERIAL);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * 后台查询
     * @return array|bool
     */
    public function searchBackend()
    {
        if (!$this->tableModel) {
            return false;
        }
        $folder = ($this->tableModel)::sortTime();
        if ($this->status) {
            $folder->andWhere(['status' => $this->status]);
        }
        $provider = new ActiveDataProvider([
            'query' => $folder,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $result_data = $provider->getModels();
        return $result_data;
    }

    /**
     * 查询缓存Key
     * @return array|null
     * @author thanatos <thanatos915@163.com>
     */
    public function getCacheKey()
    {
        if ($this->_cacheKey === null) {
            $this->_cacheKey = [
                __CLASS__,
                static::class,
                FolderMaterialTeam::tableName(),
                FolderMaterialMember::tableName(),
                $this->scenario,
                $this->attributes,
            ];
        }
        return $this->_cacheKey;
    }

    /**
     * @return int 获取用户id
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = 1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }

    /**
     * 删除查询缓存
     * @author thanatos <thanatos915@163.com>
     */
    public function removeCache()
    {
        \Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * 获取模型
     * @return bool|string
     */
    public function getTableModel()
    {
        if ($this->_tableModel === null) {
            switch ($this->method) {
                //团队素材文件夹
                case static::MATERIAL_FOLDER_TEAM:
                    $this->_tableModel = FolderMaterialTeam::class;
                    $this->_condition = ['team_id'=>$this->team_id];
                    break;
                //个人素材文件夹
                case static::MATERIAL_FOLDER_MEMBER:
                    $this->_tableModel = FolderMaterialMember::class;
                    $this->_condition = ['user_id'=>$this->user];
                    break;
                default:
                    $this->_tableModel = false;
                    break;
            }
        }
        return $this->_tableModel;
    }
}