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
            // 回退库存
            $order->detail->map(function (OrderDetail $order_detail) use ($can_payed) {
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
        $now_datetime = now()->toDateTimeString();

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
}
