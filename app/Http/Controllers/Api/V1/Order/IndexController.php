<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\PayFormEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDao;
use App\Http\Dao\TransactionDao;
use App\Http\Dao\UserAddressDao;
use App\Http\Resources\Api\OrderDetailResource;
use App\Http\Resources\Api\OrderResourceCollection;
use App\Models\Order;
use App\Models\OrderEvaluate;
use App\Models\Transaction;
use App\Models\UserAddress;
use App\Services\OrderOperateService;
use App\Utils\Wechat\WechatPayUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function cancel(Request $request, OrderDao $order_dao, OrderOperateService $order_operate_service): JsonResponse
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

            if (isset($data['can_payed']) && $data['can_payed']) {
                // 请求微信退款
                $transaction = $order
                    ->transactions()
                    ->with('payment')->where('transaction_type', Transaction::TRANSACTION_TYPE_PAY)->where('status', true)->first();

                if ($transaction instanceof Transaction) {
                    $payment = $transaction->payment;
                    // 负数 or 0
                    $old_refund_amount = $transaction->children()->sum('amount');

                    $refund_amount = bcadd($transaction->amount, $old_refund_amount, 2);

                    if ($refund_amount > 0) {
                        // 请求微信退款
                        $wechat_pay_util = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);

                        $out_refund_no = app(TransactionDao::class)->generateTransactionNo('cancel_order');

                        $reason = '用户手动取消订单，进行退款';

                        $wechat_response = $wechat_pay_util->refundOrder($transaction->transaction_no, $out_refund_no, $refund_amount, $transaction->amount, $reason);
                        $transaction->can_refund = false;
                        $transaction->save();

                        switch ($wechat_response['status'] ?? '') {
                            case 'PROCESSING': // 退款处理中
                                app(TransactionDao::class)->storeByParentTransaction($transaction, $out_refund_no, $refund_amount, remark: $reason);

                                break;

                            case 'SUCCESS': // 退款成功
                                app(TransactionDao::class)->storeByParentTransaction($transaction, $out_refund_no, $refund_amount, Transaction::STATUS_SUCCESS, remark: $validated['reason']);

                                break;

                            case 'CLOSED': // 退款关闭
                                throw new BusinessException('退款关闭，请联系管理员');

                                break;

                            case 'ABNORMAL': // 退款异常
                                throw new BusinessException('退款异常，退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台-交易中心，手动处理此笔退款');

                                break;

                            default:
                                throw new BusinessException('退款状态异常，请联系管理员');
                        }
                    }
                }
            }

            return $this->success('取消订单成功');
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
