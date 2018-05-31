<?php

use yii\db\Migration;

/**
 * Handles the creation of table `font`.
 */
class m180530_081509_create_font_table extends Migration
{
    public $tableName = '{{%font}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'font_id' => $this->primaryKey()->unsigned(),
            'font_name' => $this->string(255)->notNull()->comment('字体名称'),
            'thumbnail' => $this->string(255)->notNull()->comment('字体缩略图'),
            'thumbnail_id' => $this->integer(11)->notNull()->unsigned()->comment('缩略图ID'),
            'path' => $this->string(255)->notNull()->comment('字体原文件'),
            'path_id' => $this->integer(11)->notNull()->unsigned()->comment('原文件ID'),
            'is_official' => $this->tinyInteger(1)->notNull()->defaultValue(1)->unsigned()->comment('是否是官方字体'),
            'team_id' => $this->integer(11)->notNull()->unsigned()->comment('团队ID'),
            'copyright' => $this->tinyInteger(1)->notNull()->unsigned()->defaultValue(0)->comment('是否显示版权'),
            'group' => $this->string(20)->notNull()->defaultValue('chinese')->comment('字体分组'),
            'status' => $this->tinyInteger(1)->notNull()->unsigned()->defaultValue(20)->comment('状态'),
            'created_at' => $this->integer(11)->notNull()->unsigned()->comment('创建时间'),
        ]);

        $this->addCommentOnTable($this->tableName, '字体库');
        $this->createIndex('idx-is_official-status', $this->tableName, ['is_official', 'status']);
        $this->createIndex('idx-team_id', $this->tableName, 'team_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
