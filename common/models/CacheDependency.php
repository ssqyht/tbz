<?php

namespace common\models;

use Yii;

/**
 * 系统缓存类
 * @property string $cache_name 缓存标识
 * @property string $cache_title 缓存名
 * @property int $updated_at 最后更新时间
 * @package common\models
 * @author thanatos <thanatos915@163.com>
 */
class CacheDependency extends \yii\db\ActiveRecord
{
    const OFFICIAL_CLASSIFY = 'official_classify';
    const OFFICIAL_HOT_RECOMMEND = 'official_hot_recommend';
    const OFFICIAL_TEMPLATE = 'official_template';
    const TEMPLATE_COVER = 'template_cover';
    const MESSAGE= 'message';
    const FOLDER='folder';
    const TEMPLATE_MEMBER = 'template_member_search';
    const UPFILE = 'upfile';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cache_dependency}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cache_name', 'cache_title', 'updated_at'], 'required'],
            [['updated_at'], 'integer'],
            [['cache_name', 'cache_title'], 'string', 'max' => 50],
            [['cache_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cache_name' => '缓存标识',
            'cache_title' => '缓存名',
            'updated_at' => '最后更新时间',
        ];
    }

    /**
     * 根据缓存名生成缓存依赖sql
     * @param $name
     * @return string
     * @author thanatos <thanatos915@163.com>
     */
    public static function getDependencyCacheName($name)
    {
        return static::find()->where(['cache_name' => $name])->select('updated_at')->createCommand()->getRawSql();
    }

}
