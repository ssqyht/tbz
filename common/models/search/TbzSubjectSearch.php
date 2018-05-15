<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/9
 * Time: 16:46
 */

namespace common\models\search;

use common\models\TbzSubject;
use yii\data\ActiveDataProvider;

class TbzSubjectSearch extends \yii\base\Model
{
    /** @var string 前台 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 后台 */
    const SCENARIO_BACKEND = 'backend';
    const online_status = 1;
    const underline_status = 0;
    public function rules()
    {
        return [

        ];
    }

    /**
     * 查询数据
     * @param $params
     * @return TbzSubject[]|null
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
     * @param $status
     * @return array|bool
     * 查询数据
     */
    public function searchFrontend()
    {
       /* if (!isset($status) || $status == '') {
            $status = 1;
        }*/
        $cover_data = TbzSubject::find()
            ->where(['status' => static::online_status]);
        $provider = new ActiveDataProvider([
            'query' => $cover_data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_DESC,
                ]
            ],
        ]);
        $result_data = $provider->getModels();
        if ($result_data) {
            return $result_data;
        } else {
            return false;
        }
    }
    public function searchBackend(){

    }
}