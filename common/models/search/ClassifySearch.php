<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\search;


use common\models\Classify;
use phpDocumentor\Reflection\Types\Object_;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\components\traits\ModelErrorTrait;;
class ClassifySearch extends Model
{
    use ModelErrorTrait;
    /** @var string 前台查询 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台查询 */
    const SCENARIO_BACKEND = 'backend';
    public $category;
    public $status;
    public $classify;
    /**
     * @var array tag类型
     */
    public $tag_style = [
        1 => 'style',
        2 => 'industry',
    ];
    public function rules()
    {
        return [
            [['category','status','classify'], 'integer'],
        ];
    }
    /**
     * 查询官方模板分类表（带分页）
     * @param $params
     * @return ActiveDataProvider
     * @author thanatos <thanatos915@163.com>
     */
    public function search($params)
    {
        $query = Classify::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params, '');

        $query->andFilterWhere([
            'category' => $this->category,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

    /**
     * @return array|bool 获取小分类或标签
     */
    public function classifyTag(){
        $result = [];
        if ($this->category && !$this->classify){
            //获取小分类
            $classify_data = Classify::online()->andWhere(['category'=>$this->category])->all();
            $result['classify'] = $classify_data;
            //查询标签
            $tags = $this->searchTag($classify_data[0]->id);
            $result = array_merge($result,$tags);
            return $result;
        }elseif ($this->classify){
            //获取tag标签信息
            $result = $this->searchTag($this->classify);
            return $result;
        }else{
            $this->addError('','category或classify不能为空');
            return false;
        }
    }
    /**
     * @param $classify_id
     * @return array
     */
    public function searchTag($classify_id){
        $classify = Classify::findOne($classify_id);
        //关联表查询标签数据
        $tags_data = $classify ->tags;
        $tags = [];
        foreach ($tags_data as $value){
            $tags[$this->tag_style[$value->type]] = $value;
        }
        return $tags;
    }
}