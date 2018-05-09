<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%order}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Order"))
 *
 * @property int $order_id @SWG\Property(property="orderId", type="integer", description="")
 * @property string $pay_sn 支付单号 @SWG\Property(property="paySn", type="string", description=" 支付单号")
 * @property int $order_sn 订单号 @SWG\Property(property="orderSn", type="integer", description=" 订单号")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property int $order_purpose 订单类型 @SWG\Property(property="orderPurpose", type="integer", description=" 订单类型")
 * @property int $purpose_id 订单类型对应id @SWG\Property(property="purposeId", type="integer", description=" 订单类型对应id")
 * @property string $goods_amount 商品价格 @SWG\Property(property="goodsAmount", type="string", description=" 商品价格")
 * @property string $discount 折扣价格 @SWG\Property(property="discount", type="string", description=" 折扣价格")
 * @property string $order_amount 订单价格 @SWG\Property(property="orderAmount", type="string", description=" 订单价格")
 * @property int $order_status 订单状态 @SWG\Property(property="orderStatus", type="integer", description=" 订单状态")
 * @property int $payment_time 支付时间 @SWG\Property(property="paymentTime", type="integer", description=" 支付时间")
 * @property int $order_from 订单来源 @SWG\Property(property="orderFrom", type="integer", description=" 订单来源")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class Order extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'user_id', 'order_purpose', 'purpose_id', 'created_at', 'updated_at'], 'required'],
            [['order_sn', 'user_id', 'purpose_id', 'payment_time', 'created_at', 'updated_at'], 'integer'],
            [['goods_amount', 'discount', 'order_amount'], 'number'],
            [['pay_sn'], 'string', 'max' => 32],
            [['order_purpose', 'order_status', 'order_from'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'pay_sn' => '支付单号',
            'order_sn' => '订单号',
            'user_id' => '用户id',
            'order_purpose' => '订单类型',
            'purpose_id' => '订单类型对应id',
            'goods_amount' => '商品价格',
            'discount' => '折扣价格',
            'order_amount' => '订单价格',
            'order_status' => '订单状态',
            'payment_time' => '支付时间',
            'order_from' => '订单来源',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
