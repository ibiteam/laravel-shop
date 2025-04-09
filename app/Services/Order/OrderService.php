<?php

namespace App\Services\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PayStatusEnum;
use App\Enums\RefererEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\CartDao;
use App\Http\Dao\UserAddressDao;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @var bool 是否设置用户
     */
    private bool $is_set_user = false;

    /**
     * @var bool 是否设置用户收货地址
     */
    private bool $is_set_user_address = false;

    /**
     * @var bool 是否设置订单类型
     */
    private bool $is_set_order_type = false;

    /**
     * @var bool 是否设置商品格式化器
     */
    private bool $is_set_goods_formatters = false;

    /**
     * @var bool 是否计算价格
     */
    private bool $is_set_calculate_price = false;

    /**
     * @var bool 是否设置IP
     */
    private bool $is_set_ip = false;

    /**
     * @var bool 是否设置来源
     */
    private bool $is_set_source = false;

    /**
     * @var bool 是否设置支付方式
     */
    private bool $is_set_payment_method = false;

    /**
     * @var User 当前用户
     */
    private User $user;

    /**
     * @var UserAddress 用户选中的收货地址
     */
    private UserAddress $user_address;

    /**
     * @var OrderTypeEnum 订单类型
     */
    private OrderTypeEnum $order_type_enum;

    /**
     * @var array 商品格式化器
     */
    private array $goods_formatters;

    /**
     * @var float|int 订单商品总金额
     */
    private float|int $goods_amount = 0;

    /**
     * @var int 订单商品总积分
     */
    private int $goods_integral = 0;

    /**
     * @var float|int 订单运费
     */
    private float|int $shipping_fee = 0;

    /**
     * @var string 下单IP
     */
    private string $ip;

    /**
     * @var RefererEnum 下单来源
     */
    private RefererEnum $source;

    /**
     * @var string 支付方式
     */
    private string $payment_method;

    /**
     * @throws BusinessException
     */
    public function getInitData($user_address_id): array
    {
        if (
            ! $this->is_set_user
            || ! $this->is_set_goods_formatters
            || ! $this->is_set_calculate_price
        ) {
            throw new BusinessException('获取失败！');
        }

        $item_format = [];

        foreach ($this->getGoodsFormatters() as $goods_formatter) {
            $item_format[] = $goods_formatter->initFormat();
        }

        $payment_methods = [];

        foreach (Order::$paymentMethodMap as $alias => $name) {
            $payment_methods[] = ['name' => $name, 'alias' => $alias];
        }

        return [
            'user_address' => app(UserAddressDao::class)->showByIdOrDefault($this->getUser()->id, $user_address_id),
            'goods' => $item_format,
            'total' => [
                'goods_amount' => $this->getGoodsAmount(),
                'goods_integral' => $this->getGoodsIntegral(),
                'shipping_fee' => $this->getShippingFee(),
                'coupon' => $this->getCouponAmount(),
                'total_amount' => $this->getAmount(),
                'total_integral' => $this->getGoodsIntegral(),
            ],
            'payment_methods' => $payment_methods,
        ];
    }

    /**
     * @throws BusinessException
     * @throws \Throwable
     */
    public function store(string $remark): array
    {
        if (
            ! $this->is_set_user
            || ! $this->is_set_user_address
            || ! $this->is_set_order_type
            || ! $this->is_set_goods_formatters
            || ! $this->is_set_ip
            || ! $this->is_set_source
            || ! $this->goods_formatters
            || ! $this->is_set_calculate_price
            || ! $this->is_set_payment_method
        ) {
            throw new BusinessException('生成失败！');
        }

        $current_user = $this->getUser();
        $user_address = $this->getUserAddress();

        DB::beginTransaction();

        try {
            // 创建订单
            $order = Order::query()->create([
                'no' => $this->generateOrderSn(),
                'user_id' => $current_user->id,
                'type' => $this->getOrderTypeEnum(),
                'order_status' => OrderStatusEnum::CONFIRMED,
                'pay_status' => PayStatusEnum::PAY_WAIT,
                'ship_status' => ShippingStatusEnum::UNSHIPPED,
                'province_id' => $user_address->province,
                'city_id' => $user_address->city,
                'district_id' => $user_address->district,
                'address' => $user_address->address_detail,
                'consignee' => $user_address->recipient_name,
                'phone' => $user_address->recipient_phone,
                'goods_amount' => $this->getGoodsAmount(),
                'order_amount' => $this->getAmount(),
                'shipping_fee' => $this->getShippingFee(),
                'integral' => $this->getGoodsIntegral(),
                'coupon_amount' => 0,
                'coupon_id' => 0,
                'money_paid' => 0,
                'remark' => $remark,
                'ip' => $this->getIp(),
                'source' => $this->getSource(),
                'payment_method' => $this->getPaymentMethod(),
            ]);
            $destroy_cart_ids = [];

            foreach ($this->getGoodsFormatters() as $goods_formatter) {
                $order->detail()->create($goods_formatter->getOrderDetailFormat());

                if ($goods_formatter->isDoneDecrementStock()) {
                    $goods_formatter->decrementStock();
                }

                // 清除购物车中的下单的商品
                if ($tmp_cart_id = $goods_formatter->getCartId()) {
                    $destroy_cart_ids[] = $tmp_cart_id;
                }
            }

            if (! empty($destroy_cart_ids)) {
                app(CartDao::class)->clearDoneCartGoods($destroy_cart_ids, $current_user->id);
            }

            // todo 下单完成，判断是否需要扣除积分
            // if ($order->integral > 0) {
            //
            // }

            if ($order->order_amount == 0) {
                $order->pay_status = PayStatusEnum::PAYED;
                $order->paid_at = now()->toDateTimeString();
                $order->save();
            }

            DB::commit();

            return [
                'no' => $order->no,
                'can_pay' => $order->pay_status === PayStatusEnum::PAY_WAIT,
            ];
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw new BusinessException($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw new BusinessException('生成订单失败');
        }
    }

    public function getGoodsAmount(): float
    {
        return $this->goods_amount;
    }
    public function setGoodsAmount(float $goods_amount): void
    {
        $this->goods_amount = $goods_amount;
    }

    public function getGoodsIntegral(): int
    {
        return $this->goods_integral;
    }

    public function setGoodsIntegral(int $goods_integral): void
    {
        $this->goods_integral = $goods_integral;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->is_set_user = true;

        $this->user = $user;

        return $this;
    }

    public function getUserAddress(): UserAddress
    {
        return $this->user_address;
    }

    public function setUserAddress(UserAddress $user_address): self
    {
        $this->is_set_user_address = true;

        $this->user_address = $user_address;

        return $this;
    }

    public function getOrderTypeEnum(): OrderTypeEnum
    {
        return $this->order_type_enum;
    }

    public function setOrderTypeEnum(OrderTypeEnum $order_type_enum): self
    {
        $this->is_set_order_type = true;

        $this->order_type_enum = $order_type_enum;

        return $this;
    }

    /**
     * @return array<int, GoodsFormatter>
     */
    public function getGoodsFormatters(): array
    {
        return $this->goods_formatters;
    }

    /**
     * @param  array<int, GoodsFormatter> $goods_formatters
     * @return $this
     */
    public function setGoodsFormatters(array $goods_formatters): self
    {
        $this->is_set_goods_formatters = true;

        $this->goods_formatters = $goods_formatters;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): self
    {
        $this->is_set_payment_method = true;

        $this->payment_method = $payment_method;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->getGoodsAmount() + $this->getShippingFee();
    }

    public function getShippingFee(): float
    {
        return $this->shipping_fee;
    }

    public function setShippingFee(float $shipping_fee): self
    {
        $this->shipping_fee = $shipping_fee;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->is_set_ip = true;

        $this->ip = $ip;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source->value;
    }

    public function setSource(RefererEnum $source): self
    {
        $this->is_set_source = true;

        $this->source = $source;

        return $this;
    }

    /**
     * 计算商品总价格与商品总积分.
     *
     * @return $this
     */
    public function calculatePrice(): self
    {
        $this->is_set_calculate_price = true;

        $goods_amount = $goods_integral = 0;

        foreach ($this->getGoodsFormatters() as $goods_formatter) {
            $goods_amount += $goods_formatter->getGoodsAmount();
            $goods_integral += $goods_formatter->getGoodsIntegral();
        }
        $this->setGoodsAmount($goods_amount);
        $this->setGoodsIntegral($goods_integral);

        return $this;
    }

    private function getCouponAmount(): int|float
    {
        return 0;
    }

    /**
     * 生成订单编号.
     */
    private function generateOrderSn(): string
    {
        do {
            $no = date('Ymd').rand(100000, 999999).rand(10, 99);
        } while (Order::query()->whereNo($no)->exists());

        return $no;
    }
}
