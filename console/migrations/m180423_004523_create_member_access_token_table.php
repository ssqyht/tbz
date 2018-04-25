<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member_access_token`.
 */
class m180423_004523_create_member_access_token_table extends Migration
{

    public $tableName = '{{member_access_token}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'token_id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->comment('用户ID'),
            'access_token' => $this->string(32)->notNull()->comment('access_token'),
            'token_type' => $this->tinyInteger(1)->unsigned()->comment('登录设备号'),
            'expired_at' => $this->integer(11)->unsigned()->comment('过期时间'),
            'token_unique' => $this->string(32)->notNull()->comment('设备唯一串'),
            'created_at' => $this->integer(11)->notNull()->unsigned()->comment('创建时间'),
        ]);

        $this->addCommentOnTable($this->tableName, 'access_token管理表');

        $this->createIndex('idx-access_token-token_type-token_unique', $this->tableName, ['access_token', 'token_type', 'token_unique']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
