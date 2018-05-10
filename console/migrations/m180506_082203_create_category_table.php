<?php

use yii\db\Migration;

/**
 * 品类表
 */
class m180506_082203_create_category_table extends Migration
{

    public $tableName = '{{%category}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(11)->unsigned(),
            'name' => $this->string(10)->notNull()->comment('品类名称'),
            'class_name' => $this->string(15)->notNull()->comment('品类class名'),
            'product' => $this->string(30)->notNull()->comment('品类唯一标识'),
            'sort' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('品类排序'),
        ]);
        $this->addCommentOnTable($this->tableName, '品类表');
        $this->createIndex('idx-product', $this->tableName, 'product');

        // 添加初始记录
        $this->getDb()->createCommand()->batchInsert($this->tableName, ['name', 'class_name', 'product', 'sort'], [
            ['name' => '热门推荐', 'class_name' => 'hot', 'product' => 'recommend', 'sort' => 0],
            ['name' => '广告印刷', 'class_name' => 'hot', 'product' => 'printing', 'sort' => 0],
            ['name' => '展架画面', 'class_name' => 'hot', 'product' => 'display', 'sort' => 0],
            ['name' => '社交媒体', 'class_name' => 'hot', 'product' => 'social', 'sort' => 0],
            ['name' => '网站电商', 'class_name' => 'hot', 'product' => 'website', 'sort' => 0],
            ['name' => '商务办公', 'class_name' => 'hot', 'product' => 'commerce', 'sort' => 0],
            ['name' => '创意生活', 'class_name' => 'hot', 'product' => 'creativelife', 'sort' => 0],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
