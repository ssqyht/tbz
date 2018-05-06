<?php

use yii\db\Migration;

/**
 *
 */
class m180506_094145_create_industry_style_function_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tables = [
            'industry' => '行业',
            'style' => '风格',
            'function' => '功能',
        ];
        foreach ($tables as $table => $name) {
            $tableName = '{{%'.$table.'}}';
            $this->createTable($tableName, [
                 $table . '_id' => $this->primaryKey()->unsigned(),
                'name' => $this->string(10)->notNull()->comment($name.'名称'),
                'sort' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('排序名称'),
                'updated_at' => $this->integer(11)->notNull()->comment('修改时间'),
            ]);
            $this->addCommentOnTable($tableName, '平台'. $name . '表');

        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
