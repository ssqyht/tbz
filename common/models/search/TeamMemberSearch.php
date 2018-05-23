<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 15:48
 */
namespace common\models\search;

use common\models\Team;
use common\components\vendor\Model;
use common\models\TeamMember;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;

class TeamMemberSearch extends Model
{
    public $team_id;
    public $user_id;
    private $_cacheKey;

    public function rules()
    {
        return [
            [['team_id'], 'integer']
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['status'],
            static::SCENARIO_BACKEND => ['status'],
            static::SCENARIO_FRONTEND => ['team_id']
        ];
    }

    /**
     * @param $params
     * @return array|mixed|null
     */
    public function search($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }
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
     * 获取成员信息
     * @return bool|mixed|null
     */
    public function searchFrontend()
    {
        if (!$this->team_id) {
            $this->addError('', '团队标识team_id不能为空');
            return false;
        }
        if (!$this->isTeamMember()){
            $this->addError('', '该团队不包含此成员');
            return false;
        }
        $team_data = TeamMember::find()
            ->where(['team_id' => $this->team_id, 'status' => TeamMember::NORMAL_STATUS])
            ->orderBy(['role'=>SORT_ASC])
            ->with('memberMark');
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($team_data) {
                $result = $team_data->all();
                return $result;
            }, $this->cacheKey, CacheDependency::TEMPLATE_COVER);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }



    public function searchBackend()
    {
        return false;
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
                Team::tableName(),
                $this->scenario,
                $this->attributes,
            ];
        }
        return $this->_cacheKey;
    }
    /**
     * 获取用户id
     */
    public function getUser()
    {
        if (!$this->user_id) {
            $this->user_id = 1 /*\Yii::$app->user->id*/;
        }
        return $this->user_id;
    }

    /**
     * 删除查询缓存
     * @author thanatos <thanatos915@163.com>
     */
    public function removeCache()
    {
        \Yii::$app->cache->delete($this->cacheKey);
    }

    /**
     * 判断是否是此团队的成员
     * @return bool
     */
    public function isTeamMember()
    {
        $result = TeamMember::online()->andWhere(['user_id' => $this->user,'team_id'=>$this->team_id])->one();
        if ($result){
            return true;
        }
        return false;
    }
}