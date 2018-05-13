<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cache_dependecy`.
 */
class m180510_140232_create_cache_dependency_table extends Migration
{
    public $tableName = '{{%cache_dependency}}';
    public $group = '{{%cache_group}}';
    /**
     */
    /**
     * @return bool|void
     * @throws \yii\db\Exception
     * @author thanatos <thanatos915@163.com>
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'cache_name' => $this->string(50)->notNull()->comment('缓存标识'),
            'cache_title' => $this->string(50)->notNull()->comment('缓存名'),
            'updated_at' => $this->integer(10)->notNull()->unsigned()->comment('最后更新时间'),
            'PRIMARY KEY(cache_name)'
        ]);
        $this->addCommentOnTable($this->tableName, '系统缓存依赖表');

        $this->createTable($this->group, [
            'table_name' => $this->string(100)->notNull()->comment('表名'),
            'cache_name' => $this->string(50)->notNull()->comment('缓存名'),
            'PRIMARY KEY(table_name, cache_name)'
        ]);
        $this->addCommentOnTable($this->group, '数据缓存分组表');

        // 添加系统缓存依赖记录
        $this->getDb()->createCommand()->batchInsert($this->tableName, ['cache_name', 'cache_title', 'updated_at'], [
            ['cache_name' => 'official_classify', 'cache_title' => '官方分类缓存', 'updated_at' => time()]
        ])->execute();

        $this->getDb()->createCommand()->batchInsert($this->group, ['table_name', 'cache_name'], [
            ['table_name' => 'tu_category', 'official_classify'],
            ['table_name' => 'tu_classify', 'official_classify']
        ])->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
        $this->dropTable($this->group);
    }
}
