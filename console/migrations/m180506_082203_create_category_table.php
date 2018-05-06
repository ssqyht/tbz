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
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(10)->notNull()->comment('品类名称'),
            'class_name' => $this->string(15)->notNull()->comment('品类class名'),
            'product' => $this->string(30)->notNull()->comment('品类唯一标识'),
            'sort' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('品类排序'),
        ]);
        $this->addCommentOnTable($this->tableName, '品类表');
        $this->createIndex('idx-product', $this->tableName, 'product');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
