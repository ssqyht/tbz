<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/16
 * Time: 19:29
 */

namespace common\models\search;

use common\models\Folder;
use common\components\vendor\Model;
use common\models\MaterialFolders;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;

class FolderSearch extends Model
{
    /** @var string 模板文件夹 */
    const TEMPLATE_FOLDER = 'template_folder';
    /** @var string 素材文件夹 */
    const MATERIAL_FOLDER = 'material_folder';
    public $status;
    public $method;
    public $_user;
    private $_cacheKey;
    private $_tableModel;

    public function rules()
    {
        return [
            [['status'], 'integer'],
            ['method', 'required'],
            ['method', 'in', 'range' => [static::TEMPLATE_FOLDER, static::MATERIAL_FOLDER]],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['status','method'],
            static::SCENARIO_BACKEND => ['status','method'],
            static::SCENARIO_FRONTEND => ['method']
        ];
    }

    /**
     * @param $params
     * @return array|mixed|null
     */
    public function search($params)
    {
        $this->load($params, '');
        if (!$this->validate()){
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
            ->andWhere(['user_id' => $this->user]);
        // 查询数据 使用缓存
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($folder) {
                $result_data = $folder->all();
                return $result_data;
            }, $this->cacheKey, CacheDependency::FOLDER);
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
                Folder::tableName(),
                Folder::tableName(),
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
}