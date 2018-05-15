<?php


namespace common\models\search;

use common\components\traits\CacheDependencyTrait;
use common\models\CacheDependency;
use common\models\Category;
use common\models\TemplateOfficial;
use Yii;
use common\models\Classify;
use yii\base\Model;
use yii\caching\DbDependency;
use yii\caching\ExpressionDependency;
use yii\db\ActiveQuery;

class TemplateCenterSearch extends Model
{
    use CacheDependencyTrait;

    /** @var string 前台开启设计页 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台查询列表 */
    const SCENARIO_BACKEND = 'backend';
    const TEMPLATE_LIMIT = 12;
    /** @var array */
    private $_cacheKey;

    /**
     * 查询数据
     * @param $params
     * @return Category[]|null
     * @author thanatos <thanatos915@163.com>
     */
    public function search()
    {
       // $this->load($params, '');
        switch ($this->scenario) {
            case static::SCENARIO_FRONTEND:
                return $this->searchFrontend();
            case static::SCENARIO_BACKEND:
            case static::SCENARIO_DEFAULT:
                return null;
            default:
                return null;
        }
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
     * @return mixed|null
     */
    protected function searchFrontend()
    {
        $classify_recommend = Classify::online()
            ->andWhere(['is_recommend' => Classify::IS_RECOMMEND])
            ->with('template');
        // 查询数据 使用缓存
        try{
            $result = Yii::$app->dataCache->cache(function() use($classify_recommend) {
                $classify_recommend = $classify_recommend->all();
                $templates = [];
                foreach ($classify_recommend as $classify_value) {
                    if ($classify_value->template){
                        $templates[$classify_value->name][]= $classify_value->getTemplate()->limit(static::TEMPLATE_LIMIT)->all();
                    }
                }
                return $templates;
            }, $this->cacheKey, CacheDependency::CLASSIFY_SEARCH_TEMPLATE);
        } catch (\Throwable $e) {
            $result = null;
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
                static::class,
                Category::tableName(),
                Classify::tableName(),
                $this->scenario,
            ];
        }
        return $this->_cacheKey;
    }
    public function conditionSearch($condition)
    {
        if (!$condition || !$condition['category_id']){
            $category_id = Category::active()->one()->id;
        }else{
            $category_id = $condition['category_id'];
        }
        $result = [];
        $result['category_id']=$category_id;
        $classify_data = $this->categorySearch($category_id);//小分类数据
        if (!$classify_data){
            return false;
        }
        $result['classify'] = $classify_data;
        if (!$condition['classify_id']){
            $template_data = TemplateOfficial::online()->where(['product'=>$classify_data[0]->product])->all();//小分类查寻时，返回的模板数据
            $result['template'] = $template_data;
            return $result;
        }else{
            $classify_id = $condition['classify_id'];
        }
        $classify_search = Classify::findById($classify_id);
        $tag = $classify_search->tag;
        $tag_data= [];
        if ($tag){
            foreach ($tag as $tag_value){
                $tag_data[$tag_value->type][] = $tag_value;
            }
        }
       // $tag_data = $this->classifySearchTag($classify_id);
        $result['tag_data'] = $tag_data;

      if (!$condition['tag_id']){
            $template_data = TemplateOfficial::find()->where(['product'=>$classify_search->product])->all();
        }else{
            //$template_data = Tag::
        }
       $result['template'] = $template_data;
       return $result;
    }

    /**
     * @param $category_id
     * @return array|\yii\db\ActiveRecord[]|false
     * 大分类查询返回小分类
     */
    public function categorySearch($category_id){
        $classify_data = Classify::online()
            ->where(['category'=>$category_id])
            ->all();
        return $classify_data;
    }
    public function classifySearchTag($classify_id){
        $classify_search = Classify::findById($classify_id);
        $tag = $classify_search->tag;
        $tag_data= [];
        if ($tag){
            foreach ($tag as $tag_value){
                $tag_data[$tag_value->type][] = $tag_value;
            }
        }
        return $tag;
    }
}