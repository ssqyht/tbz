<?php

use yii\db\Migration;

/**
 * Class m180421_073158_create_center_user
 */
class m180421_073158_create_center_user extends Migration
{

    public $tableName = '{{%center_user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(30)->notNull()->defaultValue('')->comment('用户名'),
            'mobile' => $this->char(11)->notNull()->notNull()->defaultValue('')->comment('手机号'),
            'sex' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('姓别'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(10)->comment('用户状态'),
            'password_hash' => $this->char(60)->notNull()->defaultValue('')->comment('密码hash'),
            'salt' => $this->string(16)->notNull()->defaultValue('')->comment('旧salt'),
            'created_at' => $this->integer(11)->notNull()->unsigned()->comment('创建时间'),
            'updated_at' => $this->integer(11)->notNull()->unsigned()->comment('修改时间'),
        ]);

        $this->createIndex('idx-mobile', $this->tableName, 'mobile');

        $this->addCommentOnTable($this->tableName, '用户中心表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
