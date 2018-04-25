<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m180421_085904_create_member_table extends Migration
{

    public $tableName = '{{member}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'center_id' => $this->integer(11)->unsigned()->comment('用户中心ID'),
            'mobile' => $this->char(11)->notNull()->comment('用户手机号'),
            'sex' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('姓别'),
            'headimg_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('头像'),
            'coin' => $this->integer(11)->notNull()->defaultValue(0)->comment('图币'),
            'last_login_time' => $this->integer(11)->notNull()->defaultValue(0)->comment('最后登录时间'),
            'created_at' => $this->integer(11)->notNull()->unsigned()->comment('创建时间'),
            'updated_at' => $this->integer(11)->notNull()->unsigned()->comment('修改时间'),
        ]);

        $this->createIndex('idx-center_id', $this->tableName, 'center_id');

        $this->addCommentOnTable($this->tableName, '用户表');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
