<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/21
 * Time: 17:09
 */
namespace common\models\search;
use common\models\MaterialMember;
use common\models\MaterialTeam;
use common\models\TeamMember;
use common\models\TemplateMember;
use common\components\vendor\Model;
use common\models\Upfile;
use Monolog\Handler\IFTTTHandler;
use yii\data\ActiveDataProvider;
use common\models\CacheDependency;
use common\components\traits\ModelErrorTrait;
class MaterialSearch extends Model
{
    use ModelErrorTrait;
    /** @var string 个人素材 */
    const MATERIAL_MEMBER = 'material_member';
    /** @var string 团队素材 */
    const MATERIAL_TEAM = 'material_team';
    /** @var int 正常状态 */
    const NORMAL_STATUS = 10;
    /** @var integer 默认文件夹 */
    CONST DEFAULT_FOLDER = 0;
    public $status;
    public $folder;
    public $_user;
    public $sort;
    public $method;
    public $team_id;
    private $_cacheKey;
    private $_query;
    public function rules()
    {
        return [
            [['status','folder','team_id'],'integer'],
            ['method','required'],
            ['method', 'in', 'range' => [static::MATERIAL_MEMBER, static::MATERIAL_TEAM ]],
        ];
    }
    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_DEFAULT => ['status','method'],
            static::SCENARIO_BACKEND => ['status','method'],
            static::SCENARIO_FRONTEND => ['status','folder','team_id','method']
        ];
    }

    /**
     * @param $params
     * @return array|mixed|null
     */
    public function search($params)
    {
        $this->load($params, '');
        if (!$this->validate()){
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
     * 前台查询个人或团队素材
     * @return bool|mixed|null
     */
    public function searchFrontend(){
        //查询当前用户的素材
        if ($this->method == static::MATERIAL_MEMBER){
            //个人素材查询
            $this->query->andWhere(['user_id' => $this->user]);
        }else{
            //团队素材查询
            if (!$this->isTeamMember()){
                $this->addError('','当前用户不属于团队成员');
                return false;
            }
            $this->query->andWhere(['team_id' => $this->team_id]);
        }
        //按默认文件夹查询
        if (!$this->folder){
            $this->query->andWhere(['folder_id' => static::DEFAULT_FOLDER]);
        }
        $provider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 18,
            ],
        ]);
        //$this->removeCache();die;
        try {
            $result = \Yii::$app->dataCache->cache(function () use ($provider) {
                $result = $provider->getModels();
                return $result;
            }, $this->cacheKey, CacheDependency::MATERIAL);
        } catch (\Throwable $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * @return ActiveDataProvider 后台查询个人模板信息
     */
    public function searchBackend(){
        $provider = new ActiveDataProvider([
            'query' => $this->query,
        ]);
        return $provider;
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
                TemplateMember::tableName(),
                $this->scenario,
                $this->attributes,
            ];
        }
        return $this->_cacheKey;
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
     * @return mixed|\yii\db\ActiveQuery 拼接查询条件
     */
    public function getQuery()
    {
        if ($this->_query === null) {
            if ($this->method == static::MATERIAL_MEMBER){
                //个人素材
                $query = MaterialMember::sort();
            }else{
                //团队素材
                $query = MaterialTeam::sort();
            }
            //按文件夹查询
            if ($this->folder) {
                $query->andWhere(['folder_id' => $this->folder]);
            }
            //按状态查询
            if ($this->status){
                $query->andWhere(['status'=>$this->status]);
            }else{
                $query->andWhere(['status'=>static::NORMAL_STATUS]);
            }
            //按时间排序
            if (!$this->sort && $this->sort == 1){
                $query->orderBy(['created_at'=>SORT_ASC]);
            }else{
                $query->orderBy(['created_at'=>SORT_DESC]);
            }
            $this->_query = $query;
        }
        return $this->_query;
    }
    /**
     * 获取用户id
     */
    public function getUser(){
        if ($this->_user === null){
            $this->_user =1; /*\Yii::$app->user->id*/;
        }
        return $this->_user;
    }

    /**
     * 判断当前用户是否是所要查询团队的成员
     * @return bool
     */
    public function isTeamMember(){
        $result = TeamMember::findOne(['user_id'=>$this->user,'team_id'=>$this->team_id,'status'=>TeamMember::NORMAL_STATUS]);
        if ($result){
            return true;
        }
        return false;
    }
}