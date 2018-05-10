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

    public function search($status)
    {
        $cover_data = TbzSubject::find()
            ->where(['status' => $status]);
        $provider = new ActiveDataProvider([
            'query' => $cover_data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
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