<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/17
 * Time: 21:57
 */
namespace common\models\search;
use common\models\TemplateMember;
use common\components\vendor\Model;
use common\models\Upfile;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;
class UpfileSearch extends Model
{
    /** @var integer 默认文件夹 */
    CONST DEFAULT_FOLDER = 0;
    public $status;
    public $folder;
    public $_user;
    public $sort;
    private $_cacheKey;
    private $_query;
    public function rules()
    {
        return [
            [['status'],'integer']
        ];
    }
    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['status'],
            static::SCENARIO_BACKEND => ['status'],
            static::SCENARIO_FRONTEND => ['status','folder']
        ];
    }

    /**
     * @param $params
     * @return array|mixed|null
     */
    public function search($params)
    {
        $this->load($params, '');
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
    public function searchFrontend(){
        //查询当前用户的素材
        $this->query->andWhere(['user_id' => $this->user])
            ->andWhere(['team_id'=>0]);
        //按默认文件夹查询
        if (!$this->folder){
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
            }, $this->cacheKey, CacheDependency::UPFILE);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * @return ActiveDataProvider 后台查询个人模板信息
     */
    public function searchBackend(){
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
    public function getCacheKey()
    {
        if ($this->_cacheKey === null) {
            $this->_cacheKey = [
                __CLASS__,
                static::class,
                TemplateMember::tableName(),
                $this->scenario,
                $this->attributes,
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
        \Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * @return mixed|\yii\db\ActiveQuery 拼接查询条件
     */
    public function getQuery()
    {
        if ($this->_query === null) {
            $query = Upfile::sort();
            //按文件夹查询
            if ($this->folder) {
                $query->andWhere(['folder_id' => $this->folder]);
            }
            //按状态查询
            if ($this->status){
                $query->andWhere(['status'=>$this->status]);
            }else{
                $query->andWhere(['status'=>Upfile::STATUS_NORMAL]);
            }
            //按时间排序
            if (!$this->sort && $this->sort == 1){
                $query->orderBy(['created_at'=>SORT_ASC]);
            }else{
                $query->orderBy(['created_at'=>SORT_DESC]);
            }
            $this->_query = $query;
        }
        return $this->_query;
    }
    /**
     * 获取用户id
     */
    public function getUser(){
        if ($this->_user === null){
            $this->_user =1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }
}