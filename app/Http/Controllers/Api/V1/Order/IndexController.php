<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\PayPrefixEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDao;
use App\Http\Dao\TransactionDao;
use App\Http\Dao\UserAddressDao;
use App\Http\Resources\Api\OrderDetailResource;
use App\Http\Resources\Api\OrderResourceCollection;
use App\Models\Order;
use App\Models\OrderEvaluate;
use App\Models\ShopConfig;
use App\Models\Transaction;
use App\Models\UserAddress;
use App\Services\OrderOperateService;
use App\Services\Pay\PayService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $tmp_can_show_after_sales = shop_config(ShopConfig::IS_SHOW_AFTER_SALES);

        $current_user = get_user();
        $order = Order::query()
            ->withCount(['detail', 'orderDelivery'])
            ->with(['detail', 'detail.goods', 'evaluate', 'orderDelivery', 'orderDelivery.shipCompany'])
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
                ->with(['detail', 'detail.goods', 'province', 'city', 'district'])
                ->whereOrderSn($validated['order_sn'])
                ->whereUserId($current_user->id)
                ->firstOrFail();

            $order->custom_status = $order_dao->getStatusByOrder($order);

            return $this->success(OrderDetailResource::make($order));
        } catch (ModelNotFoundException) {
            return $this->error('订单不存在');
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
                ->firstOrFail();

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
        } catch (ModelNotFoundException) {
            return $this->error('订单不存在');
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
    public function addressUpdate(Request $request, OrderDao $order_dao, UserAddressDao $user_address_dao, OrderOperateService $order_operate_service): JsonResponse
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
                ->firstOrFail();

            if (! $order_dao->canEditAddress($order, true)) {
                throw new BusinessException('当前订单不允许修改收货地址');
            }

            $user_address = $user_address_dao->getUserAddressById($current_user->id, $validated['user_address_id']);

            if (! $user_address instanceof UserAddress) {
                throw new BusinessException('收货地址不存在');
            }

            $order_operate_service->updateUserAddress($order, $current_user, $user_address);

            return $this->success('修改地址成功');
        } catch (ModelNotFoundException) {
            return $this->error('订单不存在');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 删除订单.
     */
    public function destroy(Request $request, OrderDao $order_dao, OrderOperateService $order_operate_service): JsonResponse
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

            $order_operate_service->destroy($order, $current_user);

            return $this->success('删除订单成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 取消订单.
     */
    public function cancel(Request $request, OrderDao $order_dao, OrderOperateService $order_operate_service, TransactionDao $transaction_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = get_user();
            $order = Order::query()->with(['detail', 'detail.goods'])->whereUserId($current_user->id)->whereOrderSn($validated['order_sn'])->firstOrFail();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canCancel($order)) {
                throw new BusinessException('订单状态不允许取消');
            }
            $data = $order_operate_service->cancel($order, $current_user);
        } catch (ModelNotFoundException) {
            return $this->error('订单不存在');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        // 已支付，调用微信退款
        if ($data['can_payed']) {
            // 请求微信退款
            $transaction = $order
                ->transactions()
                ->with('payment')
                ->where('transaction_type', Transaction::TRANSACTION_TYPE_PAY)
                ->where('status', true)
                ->first();

            if ($transaction instanceof Transaction) {
                // 负数 or 0
                $old_refund_amount = $transaction->children()->where('status', Transaction::STATUS_SUCCESS)->sum('amount');

                $refund_amount = bcadd($transaction->amount, $old_refund_amount, 2);

                if ($refund_amount > 0) {
                    try {
                        $payment = $transaction->payment;

                        $out_refund_no = $transaction_dao->generateTransactionNo(PayPrefixEnum::USER_CANCEL_ORDER);

                        PayService::init($payment->alias)->refund($transaction, $out_refund_no, $payment, $refund_amount, '取消订单，进行退款');
                    } catch (WeChatPayException $we_chat_pay_exception) {
                        Log::error("用户取消订单：{$order->order_sn},进行微信退款失败".$we_chat_pay_exception->getMessage(), $we_chat_pay_exception->getTrace());
                    } catch (BusinessException $business_exception) {
                        Log::error("用户取消订单：{$order->order_sn},进行微信退款业务处理失败".$business_exception->getMessage(), $business_exception->getTrace());
                    } catch (\Throwable $throwable) {
                        Log::error("用户取消订单：{$order->order_sn},进行微信退款系统异常".$throwable->getMessage(), $throwable->getTrace());
                    }
                }
            }
        }

        return $this->success('取消订单成功');
    }

    /**
     * 确认收货.
     */
    public function receive(Request $request, OrderDao $order_dao, OrderOperateService $order_operate_service): JsonResponse
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
                ->firstOrFail();

            if (! $order_dao->canReceive($order)) {
                throw new BusinessException('订单状态不允许确认收货');
            }
            $order_operate_service->receive($order, $current_user);

            return $this->success('确认收货成功');
        } catch (ModelNotFoundException) {
            return $this->error('订单不存在');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
