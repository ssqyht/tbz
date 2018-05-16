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
use yii\data\ActiveDataProvider;
use common\models\Tag;
class TemplateOfficialSearch extends Model
{
    use CacheDependencyTrait;

    /** @var string 前台开启设计页 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台查询列表 */
    const SCENARIO_BACKEND = 'backend';
    const TEMPLATE_LIMIT = 12;

    /**
     * @param $params
     * @return array|bool|null|ActiveQuery
     * @throws \yii\db\Exception
     */
    public function search($params)
    {
        switch ($this->scenario) {
            case static::SCENARIO_FRONTEND:
                return $this->searchFrontend($params);
            case static::SCENARIO_BACKEND:
            case static::SCENARIO_DEFAULT:
            return $this->searchBackend($params);
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
     * @var array
     * 价格区间
     */
    public $prices = [
        1 =>['>=','price',0],
        2=> ['between','price',100,500],
        3=> ['between','price',500,1000],
        4=> ['>=','price',1000],
    ];

    /**
     * @param $params
     * @return array|bool|ActiveQuery
     * @throws \yii\db\Exception
     *
     */
    public function searchFrontend($params)
    {
        if (!$params['classify_id']){
            return false;
        }
        $classify = Classify::findById($params['classify_id']);
        //按小分类查询
        $template_data = TemplateOfficial::online()->where(['product' =>$classify->product]);
        //按价格区间查询
        if ($params['price'] && array_key_exists($params['price'],$this->prices)){
            $template_data ->andWhere(($this->prices)[$params['price']]);
        }
        //按标签类型查询
        if ($params['tag_style_id'] || $params['tag_industry_id']){
            $tag_id = $this->tagSql($params);
            if (!$tag_id){
                return false;
            }
            $template_data->andWhere(['in','template_id',$tag_id]);
        }
        //按时间或者热度排序
        if ($params['sort'] && $params['sort'] == 1){
            $template_data ->orderBy(['sort'=>SORT_DESC]);
        }else{
            $template_data ->orderBy(['updated_at'=>SORT_DESC]);
        }
        //分页
        $result = $this->paging($template_data);
       /*try {
            $result = Yii::$app->dataCache->cache(function () use ($template_data) {
               return  $this->paging($template_data);
            }, $this->cacheKey, CacheDependency::OFFICIAL_TEMPLATE);
        } catch (\Throwable $e) {
            $result = null;
        }*/
        return $result;
    }
    /**
     * @param $query
     * @return array
     * 分页
     */
    public function paging($query)
    {
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider->getModels();
    }

    /**
     * @param $params
     * @return array
     * @throws \yii\db\Exception
     */
    public function tagSql($params){
        if ($params['tag_industry_id']){
            $templates = $industry = (new \yii\db\Query())->select('template_id')->from('tu_template_official_tag')->where(['tag_id'=>$params['tag_industry_id']]);
        }
        if ($params['tag_style_id']){
            $templates = $style = (new \yii\db\Query())->select('template_id')->from('tu_template_official_tag')->where(['tag_id'=>$params['tag_style_id']]);
        }
        if ($industry && $style){
            $query = (new \yii\db\Query())->select('u.template_id');
            $templates = $query->from(['u' => $industry])->innerJoin(['s' => $style], 's.template_id = u.template_id');
        }
        $templates = $templates->all();
        $templates_id = [];
        foreach ($templates as $value){
            $templates_id [] = $value['template_id'];
        }
        return $templates_id ;
    }

    /**
     * @param $params
     * @return array|bool
     * @throws \yii\db\Exception
     * 后台查询
     */
    public function searchBackend($params){
        if (!$params['classify_id']){
            return false;
        }
        $classify = Classify::findById($params['classify_id']);
        //按小分类查询
        $template_data = TemplateOfficial::find()->where(['product' =>$classify->product]);
        //按价格区间查询
        if ($params['price'] && array_key_exists($params['price'],$this->prices)){
            $template_data ->andWhere(($this->prices)[$params['price']]);
        }
        //按标签类型查询
        if ($params['tag_style_id'] || $params['tag_industry_id']){
            $tag_id = $this->tagSql($params);
            if (!$tag_id){
                return false;
            }
            $template_data->andWhere(['in','template_id',$tag_id]);
        }
        //按转态查询
        if ($params['status']){
            $template_data ->andWhere(['status'=>$params['status']]);
        }
        //按时间或者热度查询
        if ($params['sort'] && $params['sort'] == 1){
            $template_data ->orderBy(['sort'=>SORT_DESC]);
        }else{
            $template_data ->orderBy(['updated_at'=>SORT_DESC]);
        }
        //分页
        return $this->paging($template_data);
    }
}