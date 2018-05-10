<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\search;

use common\components\traits\CacheDependencyTrait;
use common\models\CacheDependency;
use common\models\Category;
use Yii;
use common\models\Classify;
use yii\base\Model;
use yii\caching\ExpressionDependency;
use yii\db\ActiveQuery;

/**
 * Class ClassifySearch
 * @property array $cacheKey
 * @package common\models\search
 * @author thanatos <thanatos915@163.com>
 */
class CategorySearch extends Model
{
    use CacheDependencyTrait;

    /** @var string 前台开启设计页 */
    const SCENARIO_FRONTEND = 'frontend';
    const SCENARIO_BACKEND = 'backend';

    /** @var array */
    private $_cacheKey;


    public function rules()
    {
        return [];
    }

    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => [],
            static::SCENARIO_BACKEND => [],
            static::SCENARIO_FRONTEND => []
        ];
    }

    /**
     * 查询数据
     * @param $params
     * @return array|Category[]|mixed|null|\yii\db\ActiveRecord[]
     * @return Category[]|null
     * @author thanatos <thanatos915@163.com>
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
     * 查询并设置缓存
     * @param ActiveQuery $query
     * @return array|mixed|\yii\db\ActiveRecord[]
     * @author thanatos <thanatos915@163.com>
     */
    public function getCacheData(ActiveQuery $query)
    {
        $cache = Yii::$app->cache;
        $result = $cache->get($this->cacheKey);
        if ($result === false) {
            $result = $query->all();
            $cache->set($this->cacheKey, $result);
        }
        return $result;
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
                Classify::class,
                Classify::tableName(),
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
        Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * @return Category[]|null
     * @author thanatos <thanatos915@163.com>
     */
    protected function searchFrontend()
    {
        $query = Category::active();
        try {
            $result = Yii::$app->getDb()->cache(function () use ($query) {
                $query->with('classifies');
                return $query->all();
            }, null, static::getCacheDependency(CacheDependency::OFFICIAL_CLASSIFY));
        } catch (\Throwable $throwable) {
            $result = [];
        }
        return $result;
    }

    /**
     * @return Category[]|null
     * @author thanatos <thanatos915@163.com>
     * @internal
     */
    protected function searchBackend()
    {
        return $this->getCacheData(Category::active());
    }

}