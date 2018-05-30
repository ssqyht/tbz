<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/24
 * Time: 18:41
 */

namespace common\models\search;

use common\components\vendor\Model;
use common\models\TbzSubject;
use common\models\TemplateOfficial;
use common\models\TemplateTopic;
use yii\data\ActiveDataProvider;
use common\components\traits\ModelErrorTrait;
use common\models\CacheDependency;

class TemplateTopicSearch extends Model
{
    use ModelErrorTrait;

    public $status;
    public $classify_id;
    public $sort;
    public $price;
    public $topic_id;

    private $_cacheKey;
    private $_query;

    public function rules()
    {
        return [
            [['status', 'classify_id', 'sort', 'price', 'topic_id'], 'integer'],
            ['topic_id', 'required']
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['classify_id', 'sort', 'price', 'topic_id'],
            static::SCENARIO_BACKEND => ['status', 'classify_id', 'sort', 'price', 'topic_id'],
            static::SCENARIO_FRONTEND => ['classify_id', 'sort', 'price', 'topic_id']
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
     * 价格区间
     * @var array
     */
    public $prices = [
        1 => ['>', 'price', 0],
        2 => ['price' => 0],
        3 => ['price' => 0],
    ];

    /**
     * 前台查询个人或团队素材
     * @return bool|mixed|null
     */
    public function searchFrontend()
    {
        $provider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($provider) {
                $provider_model = $provider->getModels();
                $result = [];
                foreach ($provider_model as $key=>$value){
                    if ($value->templates){
                        $result[] = $value->templates;
                    }
                }
                return $result;
            }, $this->getCacheKey($provider->getKeys()), CacheDependency::TEMPLATE_TOPIC);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * @return array|bool
     */
    public function searchBackend()
    {
        /** 后台按状态查询 */
        if ($this->status){
            $query = $this->query->andWhere(['status'=>$this->status]);
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $result = $provider->getModels();
        if ($result) {
            return $result;
        }
        return false;
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
                TemplateTopic::tableName(),
                TemplateOfficial::tableName(),
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
        \Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuery()
    {
        if ($this->_query === null) {
            $query = TbzSubject::find()
                ->where(['id'=>$this->topic_id])
                ->with(['templates' => function ($query) {
                    //按小分类查询
                    if ($this->classify_id) {
                        /** @var $query \yii\db\ActiveQuery */
                        $query->andWhere(['classify_id' => $this->classify_id]);
                    }
                    //按价格查询
                    if ($this->price) {
                        $query->andWhere(($this->prices)[$this->price]);
                    }
                    //按热度查询
                    if ($this->sort == 1){
                        $query->orderBy(['sort'=>SORT_DESC]);
                    }
                }]);
            $this->_query = $query;
        }
        return $this->_query;
    }

    /**
     * 获取专题的小分类
     * @param $topic_id
     * @return array
     */
    public function getClassify($topic_id){
       $data =  TemplateTopic::find()
            ->where(['topic_id'=>$topic_id])
            ->with('classifys')
            ->all();
       $result = [];
        foreach ($data as $key=>$value){
            if ($value->classifys){
                $result[] = $value->classifys;
            }
        }
        return $result;
    }
}