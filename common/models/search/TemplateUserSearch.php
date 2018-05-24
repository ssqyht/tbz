<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/17
 * Time: 9:33
 */

namespace common\models\search;

use common\models\TemplateMember;
use common\components\vendor\Model;
use common\models\TemplateTeam;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;

class TemplateUserSearch extends Model
{
    /** @var string 个人模板 */
    const TEMPLATE_MEMBER = 'template_member';
    /** @var string 团队模板 */
    const TEMPLATE_TEAM = 'template_team';


    /** @var integer 默认文件夹 */
    const DEFAULT_FOLDER = 0;
    /** @var int 模板正常状态 */
    const NORMAL_STATUS = 10;

    public $status;
    public $classify_id;
    public $folder;
    public $sort;
    public $team_id;
    public $method;

    private $_user;
    private $_cacheKey;
    private $_query;

    public function rules()
    {
        return [
            [['status', 'team_id', 'classify_id', 'folder', 'sort'], 'integer'],
            ['method', 'required'],
            ['method', 'in', 'range' => [static::TEMPLATE_MEMBER, static::TEMPLATE_TEAM]],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['status', 'classify_id', 'method', 'team_id', 'folder', 'sort'],
            static::SCENARIO_BACKEND => ['status', 'classify_id', 'sort', 'method'],
            static::SCENARIO_FRONTEND => ['status', 'classify_id', 'folder', 'method', 'sort', 'team_id']
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
     * @return ActiveDataProvider
     */
    public function searchFrontend()
    {
        if ($this->method == static::TEMPLATE_TEAM) {
            //团队模板查询
            $this->query->andWhere(['team_id' => $this->team_id]);
        } else {
            //个人模板查询
            $this->query->andWhere(['user_id' => $this->user]);
        }

        //按默认文件夹查询
        if (!$this->folder) {
            $this->query->andWhere(['folder_id' => static::DEFAULT_FOLDER]);
        }
        $provider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 18,
            ],
        ]);
        //$this->removeCache();die;
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($provider) {
                $result = $provider->getModels();
                return $result;
            }, $this->getcacheKey($provider->getKeys()), CacheDependency::TEMPLATE_USER);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * @return ActiveDataProvider 后台查询个人模板信息
     */
    public function searchBackend()
    {
        $provider = new ActiveDataProvider([
            'query' => $this->query,
        ]);
        return $provider;
    }
    /**
     * 查询缓存Key
     * @return array|null
     * @author thanatos <thanatos915@163.com>
     */
    public function getCacheKey($key)
    {
        if ($this->_cacheKey === null) {
            $this->_cacheKey = [
                __CLASS__,
                static::class,
                TemplateMember::tableName(),
                $this->scenario,
                $this->attributes,
                $key,
            ];
        }
        return $this->_cacheKey;
    }

    /**
     * 删除查询缓存
     * @author thanatos <thanatos915@163.com>
     */
    public function removeCache()
    {
        \Yii::$app->cache->delete($this->_cacheKey);
    }

    /**
     * @return mixed|\yii\db\ActiveQuery 拼接查询条件
     */
    public function getQuery()
    {
        if ($this->_query === null) {
            if ($this->method == static::TEMPLATE_TEAM) {
                //团队模板查询
                $query = TemplateTeam::sort();
            } else {
                //个人模板查询
                $query = TemplateMember::sort();
            }
            //按小分类查询
            if ($this->classify_id) {
                $query->where(['classify_id' => $this->classify_id]);
            }
            //按文件夹查询
            if ($this->folder) {
                $query->andWhere(['folder_id' => $this->folder]);
            }
            //按状态查询
            if ($this->status) {
                $query->andWhere(['status' => $this->status]);
            } else {
                $query->andWhere(['status' => static::NORMAL_STATUS]);
            }
            //按时间排序
            if (!$this->sort && $this->sort == 1) {
                $query->orderBy(['created_at' => SORT_ASC]);
            } else {
                $query->orderBy(['created_at' => SORT_DESC]);
            }
            $this->_query = $query;
        }
        return $this->_query;
    }

    /**
     * 获取用户id
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = 1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }

}