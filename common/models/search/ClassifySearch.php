<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\search;


use common\models\Classify;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ClassifySearch extends Model
{
    /** @var string 前台查询 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台查询 */
    const SCENARIO_BACKEND = 'backend';

    public $category;
    public $status;

    public function rules()
    {
        return [
            [['category'], 'integer'],
            [['status'], 'integer'],
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

}