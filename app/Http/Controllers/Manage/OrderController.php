<?php

namespace App\Http\Controllers\Manage;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\RefererEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\ShipCompanyDao;
use App\Http\Resources\ManageOrderResourceCollection;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShopConfig;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $no = $request->get('no');
        $user_keywords = $request->get('user_keywords');
        $goods_id = $request->get('goods_id');
        $goods_name = $request->get('goods_name');
        $consignee_keywords = $request->get('consignee_keywords');
        $order_status = $request->get('order_status');
        $pay_status = $request->get('pay_status');
        $shipping_status = $request->get('shipping_status');
        $done_start_time = $request->get('done_start_time');
        $done_end_time = $request->get('done_end_time');
        $source = $request->get('source');
        $number = (int) $request->get('number', 10);

        $list = Order::query()
            ->with('user:id,user_name')
            ->when(! is_null($no), fn (Builder $query) => $query->where('no', $no))
            ->when(! is_null($order_status), fn (Builder $query) => $query->where('order_status', $order_status))
            ->when(! is_null($pay_status), fn (Builder $query) => $query->where('pay_status', $pay_status))
            ->when(! is_null($shipping_status), fn (Builder $query) => $query->where('ship_status', $shipping_status))
            ->when(! is_null($done_start_time), fn (Builder $query) => $query->where('created_at', '>=', $done_start_time))
            ->when(! is_null($done_end_time), fn (Builder $query) => $query->where('created_at', '<=', $done_end_time))
            ->when(! is_null($source), fn (Builder $query) => $query->where('source', $source))
            ->when(! is_null($consignee_keywords), fn (Builder $query) => $query->where(function (Builder $query) use ($consignee_keywords) {
                $query->whereLike('consignee', "%$consignee_keywords%")->orWhereLike('phone', "%$consignee_keywords%");
            }))
            ->when(! is_null($goods_name), fn (Builder $query) => $query->whereHas('detail', function ($query) use ($goods_name) {
                $query->whereLike('goods_name', "%$goods_name%");
            }))
            ->when(! is_null($goods_id), fn (Builder $query) => $query->whereHas('detail', function ($query) use ($goods_id) {
                $query->where('goods_id', $goods_id);
            }))
            ->when(! is_null($user_keywords), function (Builder $query) use ($user_keywords) {
                $query->where('user_id', $user_keywords)->orWhereHas('user', function ($query) use ($user_keywords) {
                    $query->whereLike('nickname', "%$user_keywords%");
                });
            })
            ->latest()
            ->paginate($number);

        return $this->success(new ManageOrderResourceCollection($list));
    }

    /**
     * 订单详情.
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '订单ID',
            ]);

            $order = Order::query()->with(['transactions', 'detail', 'user', 'province:id,name', 'city:id,name', 'district:id,name'])->whereId($validated['id'])->first();

            return $this->success([
                'order_items' => $order->detail->map(function (OrderDetail $order_detail) {
                    if ($order_detail->goods_sku_id) {
                        $goods_stock = GoodsSku::query()->whereId($order_detail->goods_sku_id)->value('number');
                    } else {
                        $goods_stock = Goods::query()->whereId($order_detail->goods_id)->value('total');
                    }

                    return [
                        'id' => $order_detail->id,
                        'goods_no' => $order_detail->goods_no,
                        'goods_id' => $order_detail->goods_id,
                        'goods_name' => $order_detail->goods_name,
                        'sku_data' => $order_detail->goods_sku_value,
                        'goods_price' => price_number_format($order_detail->goods_price),
                        'goods_number' => $order_detail->goods_number,
                        'goods_unit' => $order_detail->goods_unit,
                        'goods_integral' => $order_detail->goods_integral,
                        'goods_stock' => $goods_stock,
                        'goods_amount' => price_number_format($order_detail->goods_amount),
                    ];
                }),
                'order_info' => [
                    'id' => $order->id,
                    'no' => $order->no,
                    'order_status' => $order->order_status,
                    'order_status_message' => OrderStatusEnum::getLabelBySource($order->order_status),
                    'pay_status' => $order->pay_status,
                    'pay_status_message' => PayStatusEnum::getLabelBySource($order->pay_status),
                    'ship_status' => $order->ship_status,
                    'ship_status_message' => ShippingStatusEnum::getLabelBySource($order->ship_status),
                    'referer' => RefererEnum::getLabelBySource($order->source),
                    'payment_method' => Order::$paymentMethodMap[$order->payment_method] ?? '',
                    'remark' => $order->remark,
                ],
                'order_time' => [
                    'created_at' => $order->created_at->toDateTimeString(),
                    'paid_at' => $order->paid_at ? $order->paid_at->toDateTimeString() : '未付款',
                    'shipped_at' => $order->shipped_at ? $order->shipped_at->toDateTimeString() : '未发货',
                    'received_at' => $order->received_at ? $order->received_at->toDateTimeString() : '未收货',
                ],
                'order_amount_data' => [
                    'order_amount' => price_number_format($order->order_amount),
                    'goods_amount' => price_number_format($order->goods_amount),
                    'shipping_fee' => price_number_format($order->shipping_fee),
                    'money_paid' => price_number_format($order->money_paid),
                    'integral' => $order->integral,
                ],
                'order_pay_data' => $order->transactions->map(function (Transaction $transaction) {
                    $tmp_status = $transaction->status;
                    $message = '待处理';

                    if ($tmp_status === Transaction::STATUS_SUCCESS) {
                        if ($transaction->transaction_type === 'pay') {
                            $message = '支付成功';
                        } elseif ($transaction->transaction_type === 'refund') {
                            $message = '已退款';
                        }
                    }

                    return [
                        'transaction_no' => $transaction->transaction_no,
                        'payment_name' => $transaction->payment?->name,
                        'pay_message' => $message,
                        'remark' => $transaction->remark,
                        'amount' => price_number_format($transaction->amount),
                        'created_at' => $transaction->created_at->toDateTimeString(),
                    ];
                }),
                'order_buyer_info' => [
                    'user_name' => $order->user?->user_name,
                    'type' => RefererEnum::getLabelBySource($order->user?->source),
                    'phone' => $order->user?->phone,
                ],
                'order_consignee_info' => [
                    'consignee' => $order->consignee,
                    'address' => $order->province?->name.' '.$order->city?->name.' '.$order->district?->name.' '.$order->address,
                    'phone' => $order->phone,
                ],
                'integral_name' => shop_config(ShopConfig::INTEGRAL_NAME),
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
     * 修改发货回显数据.
     */
    public function shipEdit(Request $request, ShipCompanyDao $ship_company_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '订单ID',
            ]);
            $order = Order::query()->whereId($validated['id'])->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            return $this->success([
                'ship_company' => $ship_company_dao->getListByEnable(),
                'order_info' => [
                    'id' => $order->id,
                    'no' => $order->no,
                    'ship_status' => $order->ship_status,
                ],
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
     * 修改发货.
     */
    public function shipUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'ship_status' => 'required|integer|in:'.implode(',', [ShippingStatusEnum::SHIPPED->value, ShippingStatusEnum::UNSHIPPED->value]),
                'ship_company_id' => 'required_if:ship_status,'.ShippingStatusEnum::SHIPPED->value.'|integer',
                'ship_no' => 'required_if:ship_status,'.ShippingStatusEnum::SHIPPED->value.'|string',
            ], [
                'ship_company_id.required_if' => '当发货状态为已发货时物流公司ID必填',
                'ship_no.required_if' => '当发货状态为已发货时物流单号必填',
            ], [
                'id' => '订单ID',
                'ship_status' => '发货状态',
                'ship_company_id' => '物流公司ID',
                'ship_no' => '物流单号',
            ]);
            $order = Order::query()->whereId($validated['id'])->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if ($validated['ship_status'] == $order->ship_status) {
                throw new BusinessException('发货状态未改变');
            }

            if (! $order->update(['ship_status' => $validated['ship_status']])) {
                throw new BusinessException('修改订单发货状态失败');
            }
            // TODO 发货保存以及删除发货记录

            return $this->success('更新发货状态，添加发货记录成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
