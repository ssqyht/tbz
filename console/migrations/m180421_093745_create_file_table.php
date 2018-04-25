<?php

use yii\db\Migration;

/**
 * Handles the creation of table `file`.
 */
class m180421_093745_create_file_table extends Migration
{

    public $tableName = '{{file}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'file_id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull()->comment('用户id'),
            'team_id' => $this->integer(11)->unsigned()->notNull()->comment('团队id'),
            'file_path' => $this->string(255)->notNull()->comment('文件路径'),
            'file_type' => $this->string(10)->notNull()->comment('文件类型'),
            'file_size' => $this->integer(11)->notNull()->comment('文件大小'),
            'file_name' => $this->string(255)->notNull()->defaultValue('')->comment('文件原名'),
            'file_width' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('图片宽度'),
            'file_height' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('图片高度'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(10)->comment('文件状态'),
            'created_at' => $this->integer(11)->notNull()->unsigned()->comment('创建时间'),
            'updated_at' => $this->integer(11)->notNull()->unsigned()->comment('修改时间'),
        ]);

        $this->addCommentOnTable($this->tableName, '文件上传表');

        $this->createIndex('idx-user_id-status-team_id', $this->tableName, ['user_id', 'status', 'team_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
