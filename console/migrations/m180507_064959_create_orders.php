<?php

use yii\db\Migration;

/**
 * Class m180507_064959_create_orders
 */
class m180507_064959_create_orders extends Migration
{

    public $order = '{{%order}}';
    public $pay = '{{%order_pay}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // order
        $this->createTable($this->order, [
            'order_id' => $this->primaryKey(11)->unsigned(),
            'pay_sn' => $this->string(32)->notNull()->defaultValue('')->comment('支付单号'),
            'order_sn' => $this->bigInteger(20)->notNull()->comment('订单号'),
            'user_id' => $this->integer(11)->unsigned()->notNull()->comment('用户id'),
            'order_purpose' => $this->tinyInteger(1)->unsigned()->notNull()->comment('订单类型'),
            'purpose_id' => $this->integer(11)->unsigned()->notNull()->comment('订单类型对应id'),
            'goods_amount' => $this->decimal(8,2)->notNull()->defaultValue('0.00')->comment('商品价格'),
            'discount' => $this->decimal(8,2)->notNull()->defaultValue('0.00')->comment('折扣价格'),
            'order_amount' => $this->decimal(8,2)->notNull()->defaultValue('0.00')->comment('订单价格'),
            'order_status' => $this->tinyInteger(1)->notNull()->defaultValue(10)->comment('订单状态'),
            'payment_time' => $this->integer(10)->notNull()->defaultValue(0)->comment('支付时间'),
            'order_from' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('订单来源'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->comment('修改时间'),
        ]);

        $this->addCommentOnTable($this->order, '订单表');
        $this->createIndex('idx-order_sn', $this->order, 'order_sn');
        $this->createIndex('idx-user_id', $this->order, 'user_id');
        $this->createIndex('idx-order_status', $this->order, 'order_status');
        $this->createIndex('idx-order_from', $this->order, 'order_from');

        // order_pay
        $this->createTable($this->pay, [
            'pay_id' => $this->primaryKey(11)->unsigned(),
            'pay_sn' => $this->string(32)->notNull()->comment('支付单号'),
            'user_id' => $this->integer(11)->unsigned()->notNull()->comment('用户id'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->comment('支付状态'),
        ]);
        $this->addCommentOnTable($this->pay, '支付记录');
        $this->createIndex('idx-pay_sn', $this->pay, 'pay_sn');

    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_064959_create_orders cannot be reverted.\n";

        return false;
    }
    */
}
