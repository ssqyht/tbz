<?php

namespace common\models;

use common\components\traits\ModelErrorTrait;
use common\components\traits\ModelFieldsTrait;
use Yii;
use common\components\traits\TimestampTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Order"))
 *
 * @property int $order_id @SWG\Property(property="orderId", type="integer", description="")
 * @property int $order_sn 订单号 @SWG\Property(property="orderSn", type="integer", description=" 订单号")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property int $order_purpose 订单类型 @SWG\Property(property="orderPurpose", type="integer", description=" 订单类型")
 * @property int $purpose_id 订单类型对应id @SWG\Property(property="purposeId", type="integer", description=" 订单类型对应id")
 * @property string $goods_amount 商品价格 @SWG\Property(property="goodsAmount", type="string", description=" 商品价格")
 * @property string $discount 折扣价格 @SWG\Property(property="discount", type="string", description=" 折扣价格")
 * @property string $order_amount 订单价格 @SWG\Property(property="orderAmount", type="string", description=" 订单价格")
 * @property int $order_status 订单状态 @SWG\Property(property="orderStatus", type="integer", description=" 订单状态")
 * @property int $payment_time 支付时间 @SWG\Property(property="paymentTime", type="integer", description=" 支付时间")
 * @property string $payment_code 支付方式 @SWG\Property(property="paymentCode", type="string", description=" 支付方式")
 * @property string $trade_sn 第三方支付接口交易号 @SWG\Property(property="tradeSn", type="string", description=" 第三方支付接口交易号")
 * @property int $order_from 订单来源 @SWG\Property(property="orderFrom", type="integer", description=" 订单来源")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class Order extends \yii\db\ActiveRecord
{
    use TimestampTrait;
    use ModelErrorTrait;
    use ModelFieldsTrait;

    public $admin_id;
    public $admin_name;
    public $remark;

    /** @var string 图币充值类型 */
    const PURPOSE_RECHARGE = 10;

    /** @var int 订单来源web */
    const ORDER_FROM_WEB = 1;

    /** @var int 未支付 */
    const STATUS_NOT_PAY = 10;
    /** @var int 已支付 */
    const STATUS_READY_PAY = 20;

    /** @var string 后台修改订单 */
    const SCENARIO_ADMIN = 'admin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            static::SCENARIO_ADMIN => ArrayHelper::merge($scenarios[static::SCENARIO_DEFAULT], [
                'admin_id', 'admin_name', 'remark', 'order_status'
            ]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'user_id', 'order_purpose', 'purpose_id'], 'required'],
            [['order_sn', 'user_id', 'purpose_id', 'payment_time', 'created_at', 'updated_at', 'admin_id'], 'integer'],
            [['goods_amount', 'discount', 'order_amount'], 'number'],
            [['order_purpose', 'order_status', 'order_from'], 'number'],
            [['payment_code'], 'string', 'max' => 20],
            [['trade_sn'], 'string', 'max' => 50],
            [['order_purpose'], 'unique', 'targetAttribute' => ['order_purpose', 'purpose_id']],
            [['admin_name', 'remark'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_sn' => '订单号',
            'user_id' => '用户id',
            'order_purpose' => '订单类型',
            'purpose_id' => '订单类型对应id',
            'goods_amount' => '商品价格',
            'discount' => '折扣价格',
            'order_amount' => '订单价格',
            'order_status' => '订单状态',
            'payment_time' => '支付时间',
            'payment_code' => '支付方式',
            'trade_sn' => '第三方支付接口交易号',
            'order_from' => '订单来源',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }


    /**
     * 保存订单
     * @param $params
     * @return bool|Order
     * @author thanatos <thanatos915@163.com>
     */
    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }
        // 处理订单来源
        $this->handleOrderFrom();

        // 保存订单
        if (!$this->save()) {
            $this->addErrors($this->getErrors());
            return false;
        }
        // 后台管理员操作订单
        if ($this->scenario == static::SCENARIO_ADMIN) {
            // 处理订单操作日志
            if (!$this->saveOrderLog()) {
                return false;
            }
        }
        return $this;
    }

    public function doSuccess()
    {
        if ($this->order_status == static::STATUS_READY_PAY) {
            switch ($this->order_purpose) {
                // 充值图币订单
                case static::PURPOSE_RECHARGE:

            }
        }
    }

    /**
     * 处理订单操作日志
     * @return bool|OrderLog
     * @author thanatos <thanatos915@163.com>
     */
    private function saveOrderLog()
    {
        $model = new OrderLog();
        $model->attributes = $this->getAttributes($this->safeAttributes());
        $model->order_id = $this->order_id;
        $model->save();
        if (!$this->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }
        return $model;
    }


    /**
     * 处理订单来源
     * @author thanatos <thanatos915@163.com>
     */
    private function handleOrderFrom()
    {
        switch (Yii::$app->request->client) {
            case 'tubangzhu_web':
                $this->order_from = Order::ORDER_FROM_WEB;
        }
    }

}
