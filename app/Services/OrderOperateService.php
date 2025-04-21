<?php

namespace App\Services;

use App\Enums\ApplyRefundStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Dao\OrderLogDao;
use App\Http\Dao\UserDao;
use App\Models\ApplyRefund;
use App\Models\GoodsSku;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderOperateService
{
    /**
     * 取消订单.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function cancel(Order $order, User $user): array
    {
        DB::beginTransaction();

        try {
            // 回退积分
            if ($order->integral > 0) {
                app(UserDao::class)->incrementIntegralByDoneOrder($user, $order->integral, '用户取消订单，回退积分');
                $order->integral = 0;
                $order->save();
            }
            // 申请售后判断
            app(ApplyRefundDao::class)->getProgressDataByOrder($order, $user)->map(function (ApplyRefund $apply_refund) {
                $apply_refund->update(['status' => ApplyRefundStatusEnum::REFUND_CLOSE, 'result' => '用户取消订单，退款流程关闭']);
            });

            $can_payed = $order->order_amount > 0 && $order->pay_status === PayStatusEnum::PAYED->value;

            $order->detail->map(function (OrderDetail $order_detail) use ($can_payed) {
                // 回退库存
                $this->updateStock($order_detail, $can_payed);
            });

            // 更新订单状态
            if (! $order->update([
                'order_status' => OrderStatusEnum::CANCELLED->value,
                'pay_status' => PayStatusEnum::PAY_WAIT->value,
                'ship_status' => ShippingStatusEnum::UNSHIPPED->value,
                'paid_at' => null,
                'shipped_at' => null,
                'received_at' => null,
            ])) {
                throw new BusinessException('取消订单失败');
            }
            app(OrderLogDao::class)->storeByUser($user, $order, '取消订单');
            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('取消订单失败：'.$throwable->getMessage());

            throw new BusinessException('取消订单失败');
        }

        return ['can_payed' => $can_payed];
    }

    /**
     * 确认收货.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function receive(Order $order, User $user): void
    {
        $now_datetime = now()->format('Y-m-d H:i:s');

        DB::beginTransaction();

        try {
            if (! $order->update(['ship_status' => ShippingStatusEnum::RECEIVED->value, 'received_at' => $now_datetime])) {
                throw new BusinessException('确认收货失败');
            }

            if (! $order->orderDelivery()->update(['status' => OrderDelivery::STATUS_SUCCESS, 'received_at' => $now_datetime])) {
                throw new BusinessException('确认收货失败~');
            }

            app(OrderLogDao::class)->storeByUser($user, $order, '对订单确认了收货');

            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw new BusinessException('确认收货失败');
        }
    }

    /**
     * 删除订单.
     *
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function destroy(Order $order, User $user): void
    {
        DB::beginTransaction();

        try {
            if (! $order->delete()) {
                throw new BusinessException('删除失败');
            }

            app(OrderLogDao::class)->storeByUser($user, $order, '删除订单');
            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable) {
            DB::rollBack();

            throw new BusinessException('操作失败');
        }
    }

    /**
     * 修改订单收货地址.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function updateUserAddress(Order $order, User $user, UserAddress $user_address): void
    {
        DB::beginTransaction();

        try {
            if (! $order->update([
                'province_id' => $user_address->province,
                'city_id' => $user_address->city,
                'district_id' => $user_address->district,
                'address' => $user_address->address_detail,
                'consignee' => $user_address->consignee,
                'phone' => $user_address->phone,
                'is_edit_address' => true,
            ])) {
                throw new BusinessException('修改地址失败');
            }
            app(OrderLogDao::class)->storeByUser($user, $order, '用户修改订单收货地址');
            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable) {
            DB::rollBack();

            throw new BusinessException('修改地址失败！');
        }
    }

    /**
     * 申请售后退款成功 逻辑处理.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function applyRefund(ApplyRefund $apply_refund): void
    {
        $order = $apply_refund->order;

        if (! $order instanceof Order) {
            throw new BusinessException('订单不存在');
        }

        $order_detail = $apply_refund->orderDetail;

        if (! $order_detail instanceof OrderDetail) {
            throw new BusinessException('订单明细不存在');
        }

        $user = $apply_refund->user;

        if (! $user instanceof User) {
            throw new BusinessException('用户不存在');
        }

        DB::beginTransaction();

        try {
            // 申请售后成功状态
            $apply_refund->update([
                'status' => ApplyRefundStatusEnum::REFUND_SUCCESS->value,
                'job_time' => null,
                'result' => '退款成功，款项已原路返回买家账号',
            ]);

            // 更新订单付费金额
            $order->update(['money_paid' => $order->money_paid - $apply_refund->money]);

            // 回退积分
            if ($order_detail->goods_integral > 0) {
                $refund_integral = $order_detail->goods_integral * $apply_refund->number;
                app(UserDao::class)->incrementIntegralByDoneOrder($user, $refund_integral, '用户订单售后退款，回退积分');
                $order->integral = $order->integral - $refund_integral;
                $order->save();
            }

            $can_payed = $order->order_amount > 0 && $order->pay_status === PayStatusEnum::PAYED->value;

            // 回退库存
            $this->updateStock($order_detail, $can_payed);

            // 订单下 所有成功的售后退款
            $success_apply_refund = ApplyRefund::whereOrderId($order->id)
                ->whereStatus(ApplyRefundStatusEnum::REFUND_SUCCESS->value)
                ->select(['money', 'number'])->get();

            $success_money = $success_apply_refund->sum('money');
            $success_number = $success_apply_refund->sum('number');

            // 交易关闭：退款数量大于等于 订单明细数量时 && 退款金额 大于等于 订单金额
            if ($success_number >= $order->detail->sum('goods_number') && $success_money >= $order->order_amount) {
                // 更新订单状态
                if (! $order->update([
                    'order_status' => OrderStatusEnum::CANCELLED->value,
                    'pay_status' => PayStatusEnum::PAY_WAIT->value,
                    'ship_status' => ShippingStatusEnum::UNSHIPPED->value,
                    'paid_at' => null,
                    'shipped_at' => null,
                    'received_at' => null,
                ])) {
                    throw new BusinessException('申请售后退款，更新订单失败');
                }
            }

            app(OrderLogDao::class)->storeByUser($user, $order, '申请售后退款，更新订单');
            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('申请售后退款，更新订单异常：'.$throwable->getMessage());

            throw new BusinessException('申请售后退款，更新订单异常');
        }
    }

    /**
     * 更新回退库存.
     */
    private function updateStock(OrderDetail $order_detail, $can_payed): void
    {
        // 判断商品是否支付减库存
        $goods = $order_detail->goods;

        if ($goods) {
            if ($goods->isPayDecrementStock()) {
                if ($can_payed) {
                    if ($order_detail->goods_sku_id > 0) {
                        $goods_sku = GoodsSku::query()->whereGoodsId($goods->id)->whereId($order_detail->goods_sku_id)->first();

                        // 存在商品规格ID时，需要先判断是否存在商品规格 不存在时不进行处理
                        if ($goods_sku instanceof GoodsSku) {
                            $goods_sku->incrementStock($order_detail->goods_number);
                            $goods->incrementStock($order_detail->goods_number);
                        }
                    } else {
                        $goods->incrementStock($order_detail->goods_number);
                    }
                }

                return;
            }

            if ($order_detail->goods_sku_id > 0) {
                $goods_sku = GoodsSku::query()->whereGoodsId($goods->id)->whereId($order_detail->goods_sku_id)->first();

                if ($goods_sku instanceof GoodsSku) {
                    $goods_sku->incrementStock($order_detail->goods_number);
                    $goods->incrementStock($order_detail->goods_number);
                }
            } else {
                $goods->incrementStock($order_detail->goods_number);
            }
        }
    }
}
