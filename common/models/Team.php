<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelErrorTrait;
use common\components\traits\ModelFieldsTrait;
use yii\helpers\Url;
/**
 * This is the model class for table "{{%team}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Team"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $coin 团队图币余额 @SWG\Property(property="coin", type="integer", description=" 团队图币余额")
 * @property string $team_name 团队名称 @SWG\Property(property="teamName", type="string", description=" 团队名称")
 * @property int $founder_id 创建人id @SWG\Property(property="founderId", type="integer", description=" 创建人id")
 * @property string $colors 颜色 @SWG\Property(property="colors", type="string", description=" 颜色")
 * @property string $fonts 字体 @SWG\Property(property="fonts", type="string", description=" 字体")
 * @property string $team_mark 团队头像 @SWG\Property(property="teamMark", type="string", description=" 团队头像")
 * @property int $team_level 团队等级 @SWG\Property(property="teamLevel", type="integer", description=" 团队等级")
 * @property int $status 团队状态 @SWG\Property(property="status", type="integer", description=" 团队状态")
 * @property int $created_at 创建日期 @SWG\Property(property="createdAt", type="integer", description=" 创建日期")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class Team extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;
    use ModelErrorTrait;
    /** @var int 团队正常状态值 */
    const NORMAL_STATUS = 10;
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;
    /** @var int 删除状态 */
    const DELETE_STATUS = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coin', 'founder_id', 'team_level', 'status', 'created_at', 'updated_at'], 'integer'],
            [['team_name'], 'string', 'max' => 100],
            [['colors', 'fonts'], 'string', 'max' => 500],
            [['team_mark'], 'string', 'max' => 200],
            ['status', 'default', 'value' => 10],
            [['colors', 'fonts', 'team_mark'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '唯一标识',
            'coin' => '图币',
            'team_name' => '团队名称',
            'founder_id' => '创建者id',
            'colors' => '颜色',
            'fonts' => '字体',
            'team_mark' => '团队头像',
            'team_level' => '团队等级',
            'status' => '团队状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 正常团队
     * @return \yii\db\ActiveQuery
     */
    public static function online()
    {
        return Team::find()->where(['status' => static::NORMAL_STATUS]);
    }

    /**
     * @return array
     */
    public function frontendFields()
    {
        return [
            'id', 'team_name', 'coin', 'founder_id', 'colors', 'fonts', 'team_mark', 'team_level', 'created_at'
        ];
    }

    public function extraFields()
    {
        $data['team_mark'] = function () {
            return Url::to('@oss') . DIRECTORY_SEPARATOR . 'uploads' . $this->team_mark;
        };
        //颜色变为数组
        $data['colors'] = function () {
            return explode(',', $this->colors);
        };
        //字体变为数组
        $data ['fonts'] = function () {
            return explode(',', $this->fonts);
        };
        //团队成员
        $data['members'] = function () {
            return $this->members;
        };
        return $data;
    }

    /**
     * 关联团队成员表
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(TeamMember::class, ['team_id' => 'id'])
            ->where(['status' => TeamMember::NORMAL_STATUS])
            ->orderBy(['role' => SORT_ASC])
            ->with('memberMark');
    }
    /**
     * @param bool $insert
     * @param array $changedAttributes
     * 更新缓存
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 更新缓存
        if ($changedAttributes) {
            Yii::$app->dataCache->updateCache(static::class);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
