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
            ['cache_name' => 'official_classify', 'cache_title' => '官方分类缓存', 'updated_at' => time()],
            ['official_hot_recommend','模板中心页热门推荐',time()],
            ['folder','文件夹缓存',time()],
            ['message','消息缓存',time()],
            ['template_cover','模板专题缓存',time()],
            ['template_member_search','个人模板缓存',time()],
            ['upfile','素材缓存',time()]
        ])->execute();

        $db->createCommand()->batchInsert(CacheGroup::tableName(), ['table_name', 'cache_name'], [
            ['table_name' => 'tu_category', 'official_classify'],
            ['table_name' => 'tu_classify', 'official_classify'],
            ['template_official','official_hot_recommend'],
            ['tu_classify','official_hot_recommend'],
            ['tu_folder','folder'],
            ['tu_tbz_letter','message'],
            ['tu_tbz_subject','template_cover'],
            ['tu_template_member','template_member_search'],
            ['tu_upfile','upfile'],
        ])->execute();

        $this->stdout('Success' . "\n", Console::FG_GREEN);
    }

}