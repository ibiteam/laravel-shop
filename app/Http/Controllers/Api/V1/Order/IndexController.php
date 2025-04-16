<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDao;
use App\Http\Dao\OrderLogDao;
use App\Http\Dao\UserAddressDao;
use App\Http\Resources\Api\OrderDetailResource;
use App\Http\Resources\Api\OrderResourceCollection;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderEvaluate;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IndexController extends BaseController
{
    public const SEARCH_ALL = 'all'; // 全部
    public const SEARCH_NOT_PAY = 'not_pay'; // 未付款
    public const SEARCH_WAIT_SHIP = 'wait_ship'; // 待发货
    public const SEARCH_WAIT_RECEIVE = 'wait_receive'; // 待收货
    public const SEARCH_WAIT_EVALUATE = 'wait_evaluate'; // 待评价
    public const SEARCH_SUCCESS = 'success'; // 已完成

    /**
     * 我的订单列表.
     */
    public function index(Request $request): JsonResponse
    {
        $keywords = $request->get('keywords');
        $type = $request->get('type', self::SEARCH_ALL);
        $number = (int) $request->get('number', 10);
        $current_user = get_user();
        $order = Order::query()
            ->withCount('detail')
            ->with(['detail', 'evaluate', 'orderDelivery', 'orderDelivery.shipCompany'])
            ->latest()
            ->whereUserId($current_user->id)
            ->when(! is_null($keywords), function (Builder $query) use ($keywords) {
                $query->where(function (Builder $query) use ($keywords) {
                    $query->whereLike('order_sn', "%$keywords%")->orWhereHas('detail', function (Builder $query) use ($keywords) {
                        $query->whereLike('goods_name', "%$keywords%")->orWhereLike('goods_no', "%$keywords%");
                    });
                });
            })
            ->when($type === self::SEARCH_NOT_PAY, fn (Builder $query) => $query->searchWaitPay())
            ->when($type === self::SEARCH_WAIT_SHIP, fn (Builder $query) => $query->searchWaitShip())
            ->when($type === self::SEARCH_WAIT_RECEIVE, fn (Builder $query) => $query->searchWaitReceive())
            ->when($type === self::SEARCH_WAIT_EVALUATE, function (Builder $query) use ($current_user) {
                $evaluate_ids = OrderEvaluate::query()->whereUserId($current_user->id)->pluck('order_id')->unique()->filter()->toArray();

                $query->searchWaitEvaluate($evaluate_ids);
            })
            ->when($type === self::SEARCH_SUCCESS, fn (Builder $query) => $query->searchComplete())
            ->paginate($number);

        return $this->success(new OrderResourceCollection($order));
    }

    /**
     * 订单详情.
     */
    public function detail(Request $request, OrderDao $order_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = Order::query()
                ->withCount('orderDelivery')
                ->with(['detail', 'province', 'city', 'district'])
                ->whereOrderSn($validated['order_sn'])
                ->whereUserId($current_user->id)
                ->first();

            if (! $order) {
                throw new BusinessException('订单不存在');
            }
            $order->custom_status = $order_dao->getStatusByOrder($order);

            return $this->success(OrderDetailResource::make($order));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 修改地址回显数据.
     */
    public function addressEdit(Request $request, OrderDao $order_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = Order::query()
                ->with(['province:id,name', 'city:id,name', 'district:id,name'])
                ->whereUserId($current_user->id)
                ->whereOrderSn($validated['order_sn'])
                ->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canEditAddress($order, true)) {
                throw new BusinessException('当前订单不允许修改收货地址');
            }

            return $this->success([
                'order_sn' => $order->order_sn,
                'province_name' => $order->province?->name,
                'city_name' => $order->city?->name,
                'district_name' => $order->district?->name,
                'address' => $order->address,
                'consignee' => $order->consignee,
                'phone' => phone_hidden($order->phone),
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 修改订单地址
     *
     * @throws \Throwable
     */
    public function addressUpdate(Request $request, OrderDao $order_dao, UserAddressDao $user_address_dao, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
                'user_address_id' => 'required|integer',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();

            $order = Order::query()
                ->with(['province:id,name', 'city:id,name', 'district:id,name'])
                ->whereUserId($current_user->id)
                ->whereOrderSn($validated['order_sn'])
                ->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canEditAddress($order, true)) {
                throw new BusinessException('当前订单不允许修改收货地址');
            }

            $user_address = $user_address_dao->getUserAddressById($current_user->id, $validated['user_address_id']);

            if (! $user_address instanceof UserAddress) {
                throw new BusinessException('收货地址不存在');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('操作失败');
        }

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
            $order_log_dao->storeByUser($current_user, $order, '用户修改订单收货地址');
            DB::commit();

            return $this->success('修改地址成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            DB::rollBack();

            return $this->error('操作失败');
        }
    }

    /**
     * 删除订单.
     *
     * @throws \Throwable
     */
    public function destroy(Request $request, OrderDao $order_dao, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = $order_dao->getInfoByOrderSnAndUserId($validated['order_sn'], $current_user->id);

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canDestroy($order)) {
                throw new BusinessException('订单状态不允许删除');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        DB::beginTransaction();

        try {
            if (! $order->delete()) {
                throw new BusinessException('删除失败');
            }

            $order_log_dao->storeByUser($current_user, $order, '删除订单');
            DB::commit();

            return $this->success('删除订单成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            DB::rollBack();

            return $this->error('操作失败');
        }
    }

    /**
     * 取消订单.
     *
     * @throws \Throwable
     */
    public function cancel(Request $request, OrderDao $order_dao, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = $order_dao->getInfoByOrderSnAndUserId($validated['order_sn'], $current_user->id);

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canCancel($order)) {
                throw new BusinessException('订单状态不允许取消');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        DB::beginTransaction();

        try {
            if (! $order->update([
                'order_status' => OrderStatusEnum::CANCELLED,
                'pay_status' => PayStatusEnum::PAY_WAIT,
                'ship_status' => ShippingStatusEnum::UNSHIPPED,
                'paid_at' => null,
                'shipped_at' => null,
                'received_at' => null,
            ])) {
                throw new BusinessException('取消订单失败');
            }

            $order_log_dao->storeByUser($current_user, $order, '取消订单');

            // todo operate: 退积分以及退优惠券以及退金钱以及退库存

            DB::commit();

            return $this->success('取消订单成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            DB::rollBack();

            return $this->error('操作失败');
        }
    }

    /**
     * 确认收货.
     *
     * @throws \Throwable
     */
    public function receive(Request $request, OrderDao $order_dao, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = Order::query()
                ->with(['orderDelivery'])
                ->whereUserId($current_user->id)
                ->whereOrderSn($validated['order_sn'])
                ->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canReceive($order)) {
                throw new BusinessException('订单状态不允许确认收货');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        DB::beginTransaction();

        try {
            if (! $order->update([
                'order_status' => ShippingStatusEnum::RECEIVED,
                'received_at' => now()->toDateTimeString(),
            ])) {
                throw new BusinessException('确认收货失败');
            }

            if (! $order->orderDelivery()->update(['status' => OrderDelivery::STATUS_SUCCESS])) {
                throw new BusinessException('确认收货失败~');
            }

            $order_log_dao->storeByUser($current_user, $order, '对订单确认了收货');

            DB::commit();

            return $this->success('确认收货成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return $this->error('操作失败');
        }
    }
}
