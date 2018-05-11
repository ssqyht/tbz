<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\search;

use common\components\traits\CacheDependencyTrait;
use common\models\CacheDependency;
use common\models\Category;
use function foo\func;
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
    /** @var string 后台查询列表 */
    const SCENARIO_BACKEND = 'backend';

    public $product;

    /** @var array */
    private $_cacheKey;


    public function rules()
    {
        return [
            [['product'], 'string', 'max' => 30],
        ];
    }

    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['product'],
            static::SCENARIO_BACKEND => [],
            static::SCENARIO_FRONTEND => ['product']
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
                Category::tableName(),
                Classify::tableName(),
                $this->scenario,
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
        $query = $this->generateQuery(Category::active());
        $query->with('classifies');

        // 查询数据 使用缓存
        $result = Yii::$app->dataCache->cache(function() use($query) {
            $result = $query->all();
            // 查询热门推荐
            $result[0]->populateRelation('classifies', Classify::findHot());
            return $result;
        }, $this->cacheKey, CacheDependency::OFFICIAL_CLASSIFY);

        return $result;
    }

    /**
     * @return Category[]|null
     * @author thanatos <thanatos915@163.com>
     * @internal
     */
    protected function searchBackend()
    {
        return Yii::$app->dataCache->cache(function() {
            return $this->generateQuery(Category::active());
        }, $this->cacheKey, CacheDependency::OFFICIAL_CLASSIFY);
    }

    /**
     * 根据条件生成Query
     * @param ActiveQuery $query
     * @return ActiveQuery
     * @author thanatos <thanatos915@163.com>
     */
    protected function generateQuery(ActiveQuery $query)
    {
        if ($this->product) {
            $query->andFilterWhere([
                'product' => $this->product
            ]);
        }

        return $query;
    }

}