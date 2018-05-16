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

/**
 * Class TemplateOfficialSearch
 * @property string $cacheKey
 * @package common\models\search
 * @author thanatos <thanatos915@163.com>
 */
class TemplateOfficialSearch extends Model
{
    use CacheDependencyTrait;

    /** @var string 前台开启设计页 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台查询列表 */
    const SCENARIO_BACKEND = 'backend';
    const DEFAULT_CLASSIFY = 'mingpian';
    /** @var string 小分类 */
    public $product;
    /** @var integer 价格 */
    public $price;
    /** @var integer 风格 */
    public $tag_style_id;
    /** @var integer 行业 */
    public $tag_industry_id;
    /** @var integer 热度排序 */
    public $sort;
    /** @var integer 模板转态 */
    public $status;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['product','price','tag_style_id','tag_industry_id','sort','status'], 'integer'],
        ];
    }
    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['product','price','tag_style_id','tag_industry_id','sort','status'],
            static::SCENARIO_BACKEND => ['product','price','tag_style_id','tag_industry_id','sort','status'],
            static::SCENARIO_FRONTEND => ['product','price','tag_style_id','tag_industry_id','sort']
        ];
    }
    /**
     * @param $params
     * @return array|bool|null|ActiveQuery
     * @throws \yii\db\Exception
     */
    public function search($params)
    {
        $this->load($params,'');
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
     * 价格区间
     * @var array
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
     * 前端查询
     */
    public function searchFrontend()
    {
        //条件查询
        $template_data = $this->searchCondition();
        //线上
        $template_data->andWhere(['status'=> TemplateOfficial::STATUS_ONLINE]);
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
    public function tagSql(){
        if ($this->tag_industry_id){
            $templates = $industry = (new \yii\db\Query())->select('template_id')->from('tu_template_official_tag')->where(['tag_id'=>$this->tag_industry_id]);
        }
        if ($this->tag_style_id){
            $templates = $style = (new \yii\db\Query())->select('template_id')->from('tu_template_official_tag')->where(['tag_id'=>$this->tag_style_id]);
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
    public function searchBackend(){
        $template_data = $this->searchCondition();
        //状态查询
        if ($this->status){
            $template_data->andWhere(['status'=>$this->status]);
        }
        //分页
        return $this->paging($template_data);
    }

    /**
     * @return bool|ActiveQuery
     * @throws \yii\db\Exception
     * 拼接查询条件
     */
    public function searchCondition(){
        if (!$this->product){
            $this->product = static::DEFAULT_CLASSIFY;
        }
        //按小分类查询
        $template_data = TemplateOfficial::find()->where(['product' =>$this->product]);
        //按价格区间查询
        if ($this->price && array_key_exists($this->price,$this->prices)){
            $template_data ->andWhere(($this->prices)[$this->price]);
        }
        //按标签类型查询
        if ($this->tag_style_id || $this->tag_industry_id){
            $tag_id = $this->tagSql();
            if (!$tag_id){
                return false;
            }
            $template_data->andWhere(['in','template_id',$tag_id]);
        }
        //按时间或者热度排序
        if ($this->sort && $this->sort == 1){
            $template_data ->orderBy(['sort'=>SORT_DESC]);
        }else{
            $template_data ->orderBy(['updated_at'=>SORT_DESC]);
        }
        return $template_data;
    }
}