<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\search;

use common\models\Category;
use Yii;
use common\models\Classify;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * Class ClassifySearch
 * @property array $cacheKey
 * @package common\models\search
 * @author thanatos <thanatos915@163.com>
 */
class ClassifySearch extends Model
{
    /** @var array */
    private $_cacheKey;


    public function rules()
    {
        return [];
    }

    /**
     * 查询数据
     * @return array|null
     * @author thanatos <thanatos915@163.com>
     */
    public function search()
    {
        $query = Category::find()->orderBy(['sort' => SORT_ASC]);
        return $this->getCacheData($query);
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
}