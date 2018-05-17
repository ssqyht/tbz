<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace console\controllers;


use Yii;
use common\models\CacheDependency;
use common\models\CacheGroup;
use yii\console\Controller;
use yii\helpers\Console;

class CacheDependencyController extends Controller
{

    /**
     * 初始化缓存表数据
     * @throws \yii\db\Exception
     * @author thanatos <thanatos915@163.com>
     */
    public function actionInit()
    {
        $db = CacheDependency::getDb();
        $db->createCommand()->delete(CacheDependency::tableName())->execute();
        $db->createCommand()->delete(CacheGroup::tableName())->execute();

        // 添加系统缓存依赖记录
        $db->createCommand()->batchInsert(CacheDependency::tableName(), ['cache_name', 'cache_title', 'updated_at'], [
            ['cache_name' => 'official_classify', 'cache_title' => '官方分类缓存', 'updated_at' => time()]
        ])->execute();

        $db->createCommand()->batchInsert(CacheGroup::tableName(), ['table_name', 'cache_name'], [
            ['table_name' => 'tu_category', 'official_classify'],
            ['table_name' => 'tu_classify', 'official_classify']
        ])->execute();

        $this->stdout('Success' . "\n", Console::FG_GREEN);
    }

}