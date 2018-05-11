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
    public function rules()
    {
        return [

        ];
    }

    /**
     * @param $status
     * @return array|bool
     * 查询数据
     */
    public function search($status)
    {
        if (!isset($status) || $status == '') {
            $status = 1;
        }
        $cover_data = TbzSubject::find()
            ->where(['status' => $status]);
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
}