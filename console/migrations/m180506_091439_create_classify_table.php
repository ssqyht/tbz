<?php

use yii\db\Migration;

/**
 * 分类信息表
 */
class m180506_091439_create_classify_table extends Migration
{
    public $tableName = "{{%classify}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(11)->unsigned(),
            'product' => $this->string(30)->notNull()->comment('模板分类标识'),
            'parent_product' => $this->string(30)->notNull()->defaultValue('')->comment('父分类'),
            'category' => $this->string(30)->notNull()->comment('所属品类标识'),
            'name' => $this->string(10)->notNull()->comment('分类名称'),
            'parent_name' => $this->string(10)->notNull()->comment('父分类名称'),
            'default_price' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('默认价格'),
            'is_hot' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否是热门'),
            'is_new' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否是新上'),
            'default_edit' => $this->text()->notNull()->comment('模板默认配置'),
            'order_link' => $this->string(255)->notNull()->defaultValue('')->comment('下单连接'),
            'thumbnail' => $this->string(255)->notNull()->defaultValue('')->comment('缩略图'),
            'thumbnail_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0)->comment('缩略图file_id'),
            'sort' => $this->smallInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('排序值'),
            'is_open' => $this->tinyInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('是否对外开放'),
            'status' => $this->tinyInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('分类状态'),
            'created_at' => $this->integer(10)->notNull()->comment('创建时间'),
            'updated_at' => $this->integer(10)->notNull()->comment('修改时间')
        ]);
        $this->addCommentOnTable($this->tableName, '分类信息表');
        $this->createIndex('idx-product', $this->tableName, 'product');
        $this->createIndex('idx-category', $this->tableName, 'category');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
