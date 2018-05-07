<?php

use yii\db\Migration;

/**
 * Handles the creation of table `file_used_record`.
 */
class m180426_233732_create_file_used_record_table extends Migration
{
    public $tableName = '{{%file_used_record}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(11)->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull()->comment('用户ID'),
            'file_id' => $this->integer(11)->unsigned()->notNull()->comment('文件ID'),
            'purpose' => $this->tinyInteger(1)->unsigned()->notNull()->comment('用例'),
            'purpose_id' => $this->integer(11)->unsigned()->notNull()->comment('用途ID'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->comment('登录时间'),
        ]);
        $this->addCommentOnTable($this->tableName, '文件使用记录表');
        $this->createIndex('idx-user_id-file_id-purpose-purpose_id', $this->tableName, [
            'user_id', 'file_id', 'purpose', 'purpose_id'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
