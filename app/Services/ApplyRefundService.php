<?php

namespace App\Services;

use App\Enums\ApplyRefundStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Dao\ApplyRefundLogDao;
use App\Jobs\Order\ApplyRefundJob;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use App\Models\ApplyRefundReason;
use App\Models\ApplyRefundShip;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShopConfig;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApplyRefundService
{
    /**
     * 初始化申请售后信息.
     *
     * @throws BusinessException
     */
    public function init(User $user, string $order_no, int $order_detail_id): array
    {
        $order = Order::query()->whereNo($order_no)->whereUserId($user->id)->first();

        if (! $order instanceof Order) {
            throw new BusinessException('订单未找到');
        }

        if ($order->order_status != OrderStatusEnum::CONFIRMED->value || $order->pay_status != PayStatusEnum::PAYED->value) {
            // 未确认订单或未付款订单 不能申请售后
            throw new BusinessException('订单状态不允许申请售后');
        }

        $order_detail = OrderDetail::query()->with('goods')->whereId($order_detail_id)->whereOrderId($order->id)->first();

        if (! $order_detail instanceof OrderDetail) {
            throw new BusinessException('订单明细不存在');
        }

        $tmp_goods_price = get_new_price($order_detail->goods_price);

        $tmp_refund_type = [
            [
                'label' => '我要退款（无需退货）',
                'value' => ApplyRefund::TYPE_REFUND_MONEY,
                'desc' => '已与商家协商一致，只退款，不退货',
            ],
        ];

        if ($order->ship_status != ShippingStatusEnum::UNSHIPPED->value) {
            $tmp_refund_type[] = [
                'label' => '我要退货退款',
                'value' => ApplyRefund::TYPE_REFUND_GOODS,
                'desc' => '需要退还收到的货物',
            ];
        }

        return [
            'goods_info' => [
                'order_detail_id' => $order_detail->id,
                'order_no' => $order_no,
                'goods_no' => $order_detail->goods_no,
                'goods_name' => $order_detail->goods_name,
                'goods_image' => $order_detail->goods?->image,
                'goods_number' => $order_detail->goods_number,
                'goods_price' => $tmp_goods_price,
                'goods_price_format' => price_format($tmp_goods_price),
                'goods_integral' => $order_detail->goods_integral,
                'goods_amount' => $order_detail->goods_amount,
                'goods_sku_id' => $order_detail->goods_sku_id,
                'goods_sku_value' => $order_detail->goods_sku_value,
                'goods_unit' => $order_detail->goods_unit,
                'is_show_after_sales' => app(ApplyRefundDao::class)->showAfterSales($order_detail),  // 申请售后按钮状态
            ],
            'refund_type' => $tmp_refund_type,
        ];
    }

    /**
     * 根据售后类型获取申请售后信息.
     *
     * @throws BusinessException
     */
    public function getInfoByTypeAndOrder(User $user, string $order_no, int $order_detail_id, int $type): array
    {
        $order = Order::query()->whereNo($order_no)->whereUserId($user->id)->first();

        if (! $order instanceof Order) {
            throw new BusinessException('订单未找到');
        }

        $order_detail = OrderDetail::query()->with('goods')->whereId($order_detail_id)->whereOrderId($order->id)->first();

        if (! $order_detail instanceof OrderDetail) {
            throw new BusinessException('订单明细不存在');
        }

        // 当前订单明细是否已经存在退款中的申请售后
        $apply_refund = ApplyRefund::query()
            ->whereUserId($user->id)
            ->whereOrderId($order->id)
            ->whereOrderDetailId($order_detail_id)
            ->whereIn('status', ApplyRefund::$statusInProgressMap)
            ->orderByDesc('created_at')
            ->first();

        if ($apply_refund instanceof ApplyRefund) {
            throw new BusinessException('存在退款中的申请售后');
        }

        $temp_order_detail = [
            'goods_no' => $order_detail->goods_no,
            'goods_name' => $order_detail->goods_name,
            'goods_image' => $order_detail->goods?->image,
            'goods_price' => price_format($order_detail->goods_price),
            'goods_integral' => $order_detail->goods_integral,
            'goods_number' => get_new_price($order_detail->goods_number),
            'goods_unit' => $order_detail->goods_unit,
            'goods_sku_id' => $order_detail->goods_sku_id,
            'goods_sku_value' => $order_detail->goods_sku_value,
            'goods_amount' => get_new_price($order_detail->goods_amount),
            'goods_amount_format' => price_format($order_detail->goods_amount),
            'order_no' => $order->no,
            'shipping_fee' => $order->shipping_fee,
            'pay_time' => $order->paid_at,
        ];

        [$refund_max_amount, $refund_max_number] = app(ApplyRefundDao::class)->getMaxAmountAndNumber($order_detail, $order, $user);
        $temp_order_detail['refund_max_amount'] = $refund_max_amount;
        $temp_order_detail['refund_max_number'] = $refund_max_number;

        $reason = ApplyRefundReason::query()->whereType($type)->orderByDesc('sort')->select(['id', 'content'])->get()->toArray();

        $explain = '1、订单退款后，退款金额将按支付方式原路返回，订单关闭；
2、订单关闭后，无法恢复；
3、如订单已使用的优惠券，订单关闭后优惠券不返还；
4、如遇订单拆分，部分订单退款后优惠券不返还。';

        return [
            'type' => $type,
            'order_detail' => $temp_order_detail,
            'reason' => $reason,
            'explain' => $explain,
        ];
    }

    /**
     * 发起申请售后.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function launchRefund(User $user, array $params)
    {
        $apply_refund_id = $params['apply_refund_id'] ?? 0;
        $order_no = $params['order_no'];
        $order_detail_id = $params['order_detail_id'];
        $type = $params['type'];
        $money = $params['money'];
        $number = $params['number'];
        $reason_id = $params['reason_id'];
        $description = $params['description'] ?? null;
        $certificate = $params['certificate'] ?? null;

        if ($apply_refund_id) {
            // 修改申请售后
            $apply_refund = ApplyRefund::query()->with(['order', 'orderDetail'])->whereUserId($user->id)->whereId($apply_refund_id)->first();

            if (! $apply_refund instanceof ApplyRefund) {
                throw new BusinessException('申请售后不存在');
            }

            if ($apply_refund->status != ApplyRefundStatusEnum::REFUSE->value) {
                throw new BusinessException('请先等待商家操作');
            }

            $order = $apply_refund->order;

            if (! $order instanceof Order || $order->pay_status != PayStatusEnum::PAYED->value) {
                throw new BusinessException('订单已不支持退款');
            }

            $order_detail = $apply_refund->orderDetail;

            if (! $order_detail instanceof OrderDetail) {
                throw new BusinessException('订单明细不存在');
            }

            $reason_message = '修改了申请售后';
            $count = $apply_refund->count + 1;
        } else {
            // 新增申请售后
            $order = Order::query()->with('user')->whereUserId($user->id)->whereNo($order_no)->wherePayStatus(PayStatusEnum::PAYED->value)->first();

            if (! $order) {
                throw new BusinessException('订单已不支持退款');
            }

            $order_detail = OrderDetail::query()->whereOrderId($order->id)->whereId($order_detail_id)->first();

            if (! ($order_detail instanceof OrderDetail)) {
                throw new BusinessException('订单明细不存在');
            }

            $exists_apply_refund = ApplyRefund::query()
                ->whereUserId($user->id)
                ->whereOrderId($order->id)
                ->whereOrderDetailId($order_detail->id)
                ->whereIn('status', ApplyRefund::$statusInProgressMap)
                ->first();

            // 只能存在一个退款进行中的申请售后
            if ($exists_apply_refund instanceof ApplyRefund) {
                throw new BusinessException('您正在退款中');
            }

            $apply_refund = new ApplyRefund;
            $reason_message = '发起了申请售后';
            $count = 1;
        }

        // 售后超时检测
        app(ApplyRefundDao::class)->checkTimeliness($order);

        // 售后最大退款金额
        $after_sales_max_money = floatval(shop_config(ShopConfig::AFTER_SALES_MAX_MONEY));

        if ($after_sales_max_money && $money > $after_sales_max_money) {
            throw new BusinessException('退款金额不能超过'.$after_sales_max_money);
        }

        [$refund_max_amount, $refund_max_number] = app(ApplyRefundDao::class)->getMaxAmountAndNumber($order_detail, $order, $user, $apply_refund_id);

        if ($refund_max_amount == 0) {
            throw new BusinessException('可退款金额为0，暂不支持申请');
        }

        if ($refund_max_number == 0) {
            throw new BusinessException('可退款数量为0，暂不支持申请');
        }

        if ($money > $refund_max_amount) {
            throw new BusinessException('最多退款金额为'.$refund_max_amount);
        }

        if ($number > $refund_max_number) {
            throw new BusinessException('最多退款数量为'.$refund_max_number);
        }

        $seller_shipped_time = intval(shop_config(ShopConfig::SELLER_SHIPPED_TIME)); // 卖家未发货退款申请响应时间（小时）
        $seller_confirm_time = intval(shop_config(ShopConfig::SELLER_CONFIRM_TIME)); // 卖家处理响应时间（小时）

        if ($type == ApplyRefund::TYPE_REFUND_GOODS) {
            $delayed_time = Carbon::now()->addHours($seller_shipped_time);
        } else {
            $delayed_time = Carbon::now()->addHours($seller_confirm_time);
        }

        $data = [
            'no' => get_flow_sn(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'order_detail_id' => $order_detail->id,
            'type' => $type,
            'status' => ApplyRefundStatusEnum::NOT_PROCESSED->value,
            'money' => $money,
            'number' => $number,
            'reason_id' => $reason_id,
            'description' => $description,
            'certificate' => $certificate,
            'job_time' => $delayed_time,
            'count' => $count,
        ];

        DB::beginTransaction();

        try {
            $apply_refund->fill($data)->save();

            app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $user->nickname, $reason_message, ApplyRefundLog::TYPE_BUYER);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw new BusinessException('申请售后失败');
        }

        // 卖家超时未处理，自动退款给买家
        ApplyRefundJob::dispatch(ApplyRefundStatusEnum::NOT_PROCESSED->value, $apply_refund->id, '卖家超时未处理，自动退款给买家', ApplyRefundLog::TYPE_BUYER)->delay($delayed_time)->onQueue(config('cache.prefix'));

        return $apply_refund;
    }

    /**
     * 根据 申请售后id 或 订单id,订单明细id 获取申请售后信息.
     *
     * @throws BusinessException
     */
    public function getDetailByOrderOrId(User $user, int $apply_refund_id, string $order_no, int $order_detail_id): array
    {
        if ($apply_refund_id) {
            $apply_refund = ApplyRefund::query()
                ->with(['order', 'orderDetail', 'applyRefundShip', 'applyRefundReason' => function ($query) {
                    $query->withTrashed();
                }])
                ->whereUserId($user->id)->whereId($apply_refund_id)
                ->first();

            if (! $apply_refund instanceof ApplyRefund) {
                throw new BusinessException('申请售后不存在');
            }

            $order = $apply_refund->order;

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            $order_detail = $apply_refund->orderDetail;

            if (! $order_detail instanceof OrderDetail) {
                throw new BusinessException('订单明细不存在');
            }
        } else {
            $order = Order::query()->with('user')->whereUserId($user->id)->whereNo($order_no)->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            $order_detail = OrderDetail::query()->whereOrderId($order->id)->whereId($order_detail_id)->first();

            if (! $order_detail instanceof OrderDetail) {
                throw new BusinessException('订单明细不存在');
            }

            $apply_refund = ApplyRefund::query()
                ->with(['order', 'orderDetail', 'applyRefundShip', 'applyRefundReason' => function ($query) {
                    $query->withTrashed();
                }])
                ->whereUserId($user->id)->whereOrderId($order->id)->whereOrderDetailId($order_detail->id)
                ->orderByDesc('id')
                ->first();

            if (! $apply_refund instanceof ApplyRefund) {
                throw new BusinessException('申请售后不存在');
            }
        }

        return $this->detailFormat($apply_refund, $order, $order_detail, $user);
    }

    /**
     * 撤销申请.
     *
     * @throws BusinessException
     */
    public function revoke(User $user, int $apply_refund_id): void
    {
        $apply_refund = ApplyRefund::whereId($apply_refund_id)->whereUserId($user->id)->first();

        if (! $apply_refund instanceof ApplyRefund) {
            throw new BusinessException('退款信息不存在');
        }

        if ($apply_refund->is_revoke == ApplyRefund::REVOKE_YES) {
            throw new BusinessException('您的撤销次数已经使用，无法撤销');
        }

        if (in_array($apply_refund->status, [ApplyRefundStatusEnum::REFUSE->value, ApplyRefundStatusEnum::REFUND_SUCCESS->value, ApplyRefundStatusEnum::REFUND_CLOSE->value])) {
            throw new BusinessException('退款状态不支持撤销');
        }

        $apply_refund->status = ApplyRefundStatusEnum::REFUND_CLOSE->value;
        $apply_refund->is_revoke = ApplyRefund::REVOKE_YES;
        $apply_refund->result = '';
        $apply_refund->save();

        // 添加日志
        app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $user->nickname, '因买家撤销退款申请，退款已关闭', ApplyRefundLog::TYPE_BUYER);
    }

    private function detailFormat(ApplyRefund $apply_refund, Order $order, OrderDetail $order_detail, User $user): array
    {
        $temp_order_detail = [
            'goods_no' => $order_detail->goods_no,
            'goods_name' => $order_detail->goods_name,
            'goods_image' => $order_detail->goods?->image,
            'goods_price' => price_format($order_detail->goods_price),
            'goods_integral' => $order_detail->goods_integral,
            'goods_number' => get_new_price($order_detail->goods_number),
            'goods_unit' => $order_detail->goods_unit,
            'goods_sku_id' => $order_detail->goods_sku_id,
            'goods_sku_value' => $order_detail->goods_sku_value,
            'goods_amount' => get_new_price($order_detail->goods_amount),
            'goods_amount_format' => price_format($order_detail->goods_amount),
            'order_no' => $order->no,
            'shipping_fee' => $order->shipping_fee,
            'pay_time' => $order->paid_at,
        ];

        $temp_comment = $apply_refund->result;
        $from_init = null;
        $address = null;

        if ($apply_refund->status == ApplyRefundStatusEnum::REFUSE->value) {
            $temp_comment = '拒绝原因：'.$apply_refund->result;

            [$refund_max_amount, $refund_max_number] = app(ApplyRefundDao::class)->getMaxAmountAndNumber($order_detail, $order, $user, $apply_refund->id);
            $from_init = [
                'reason' => ApplyRefundReason::query()->whereType($apply_refund->type)->orderByDesc('sort')->select(['id', 'content'])->get()->toArray(),
                'refund_max_amount' => $refund_max_amount,
                'refund_max_number' => $refund_max_number,
            ];
        } elseif ($apply_refund->status == ApplyRefundStatusEnum::REFUSE_EXAMINE->value) {
            $apply_refund_shipping = ApplyRefundShip::query()->whereApplyRefundId($apply_refund->id)->first();

            if ($apply_refund_shipping) {
                $address['apply_refund_shipping'] = $apply_refund_shipping->toArray();
            }
        } elseif ($apply_refund->status == ApplyRefundStatusEnum::REFUND_SUCCESS->value) {
            $temp_comment = '款项已原路退回 ¥'.$apply_refund->money;
        } elseif ($apply_refund->status == ApplyRefundStatusEnum::REFUND_CLOSE->value) {
            $temp_comment = '卖家已发货，退款流程关闭，交易正常进行';
            $apply_refund_log = ApplyRefundLog::whereApplyRefundId($apply_refund->id)->orderByDesc('id')->first();

            if ($apply_refund_log) {
                $temp_comment = $apply_refund_log->action;
            }
        }

        return [
            'refund_info' => [
                'id' => $apply_refund->id,
                'no' => $apply_refund->no,
                'status' => $apply_refund->status,
                'money_format' => price_format($apply_refund->money),
                'money' => get_new_price($apply_refund->money),
                'number' => get_new_price($apply_refund->number),
                'reason_id' => $apply_refund->reason_id,
                'is_revoke' => $apply_refund->is_revoke,
                'description' => $apply_refund->description,
                'certificate' => $apply_refund->certificate,
                'apply_refund_shipping_id' => $apply_refund->applyRefundShip?->id,
                'apply_refund_shipping_no' => $apply_refund->applyRefundShip?->no,
                'updated_at' => $apply_refund->updated_at->format('Y-m-d H:i:s'),
                'result' => $temp_comment,
                'job_time' => $apply_refund->job_time ? strtotime($apply_refund->job_time) : '',
                'system_time' => time(),
            ],
            'type' => $apply_refund->type,
            'address' => $address,
            'order_detail' => $temp_order_detail,
            'from_init' => $from_init,
        ];
    }
}
