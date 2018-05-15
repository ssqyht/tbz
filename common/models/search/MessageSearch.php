<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/11
 * Time: 11:42
 */
namespace common\models\search;
use common\models\TbzLetter;
use yii\base\Model;
use yii\data\ActiveDataProvider;
class MessageSearch extends Model
{
    public function Search($status){
        $tbz_letter =  TbzLetter::find()
            ->where(['status'=>$status]);
        $provider = new ActiveDataProvider([
            'query' => $tbz_letter,
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