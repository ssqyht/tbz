<?php

use yii\db\Migration;

/**
 * Handles the creation of table `material_member`.
 */
class m180522_113333_create_material_member_table extends Migration
{
    public $tableName = '{{%material_member}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull()->comment('用户id'),
            'folder_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('文件夹'),
            'title' => $this->string(255)->notNull()->defaultValue('')->comment('素材标题'),
            'thumbnail' => $this->string(255)->notNull()->defaultValue('')->comment('图片路径'),
            'file_id' => $this->integer(11)->notNull()->defaultValue(0)->unsigned()->comment('文件id'),
            'mode' => $this->integer(11)->notNull()->defaultValue(0)->unsigned()->comment('素材模式 临时，正式'),
            'created_at' => $this->integer(11)->notNull()->comment('创建时间')
        ]);
        $this->createIndex('idx-user_id-folder_id', $this->tableName, ['user_id', 'folder_id']);
        $this->addCommentOnTable($this->tableName, '用户素材列表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
