<?php

namespace App\Jobs\Order;

use App\Enums\ApplyRefundStatusEnum;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Dao\ApplyRefundLogDao;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ApplyRefundJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $status;             // 退款状态：0待处理；1已拒绝退款；2退货审核成功待买家发货；3买家已发货待卖家收货
    public int $apply_refund_id;    // 申请退款id
    public string $action;          // 操作行为
    public int $type;               // 类型：0买方；1卖方

    /**
     * Create a new job instance.
     */
    public function __construct($status, $apply_refund_id, $action, $type)
    {
        $this->status = $status;
        $this->apply_refund_id = $apply_refund_id;
        $this->action = $action;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle(): bool
    {
        $apply_refund = ApplyRefund::query()->whereId($this->apply_refund_id)->first();

        if (! $apply_refund) {
            return false;
        }

        switch ($this->status) {
            case ApplyRefundStatusEnum::NOT_PROCESSED->value: // 状态:待处理
                if ($apply_refund->status == ApplyRefundStatusEnum::NOT_PROCESSED->value) {
                    $this->applyRefund($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::REFUSE->value: // 状态: 已拒绝退款
                if ($apply_refund->status == ApplyRefundStatusEnum::REFUSE->value) {
                    $this->applyRefundClose($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::REFUSE_EXAMINE->value: // 状态: 退货审核成功 待买家发货
                if ($apply_refund->status == ApplyRefundStatusEnum::REFUSE_EXAMINE->value) {
                    $this->applyRefundClose($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::BUYER_SEND_SHIP->value:  // 状态: 买家已发货 待卖家收货
                if ($apply_refund->status == ApplyRefundStatusEnum::BUYER_SEND_SHIP->value) {
                    $this->applyRefund($this->action, $this->type, $this->status);
                }

                break;

            default:
                break;
        }

        return true;
    }

    /**
     * 关闭退款.
     *
     * @throws \Throwable
     */
    public function applyRefundClose($action, $type, $status): void
    {
        $apply_refund = ApplyRefund::query()->with(['user'])->whereId($this->apply_refund_id)->first();

        $apply_refund->status = ApplyRefundStatusEnum::REFUND_CLOSE->value;

        DB::beginTransaction();

        try {
            $apply_refund->save();

            // 买家超时未申请，不记录日志
            if ($status === ApplyRefundStatusEnum::REFUSE_EXAMINE->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $apply_refund->user?->user_name, $action, $type);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // AliLogUtil::error('关闭退款失败。申请退款状态：'.$status.'申请退款记录id:'.$apply_refund->id.$exception->getMessage());
        }
    }

    /**
     * 执行退款.
     *
     * @throws \Throwable
     */
    public function applyRefund($action, $type, $status): void
    {
        $apply_refund = ApplyRefund::query()->with(['user'])->whereId($this->apply_refund_id)->first();

        $apply_refund->status = ApplyRefundStatusEnum::REFUND_SUCCESS->value;
        $apply_refund->result = '卖家超时未处理，退款成功';

        DB::beginTransaction();

        try {
            $apply_refund->save();

            // 发起申请售后 超时未处理 增加2条记录
            if ($status === ApplyRefundStatusEnum::NOT_PROCESSED->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $apply_refund->user?->user_name, '卖家超时未处理', ApplyRefundLog::TYPE_SELLER);
            }

            if ($status === ApplyRefundStatusEnum::BUYER_SEND_SHIP->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $apply_refund->user?->user_name, '卖家超时未确认收货', ApplyRefundLog::TYPE_SELLER);
            }
            app(ApplyRefundLogDao::class)->addLog($apply_refund->id, $apply_refund->user?->user_name, $action, $type);

            // TODO 退款操作

            // // 支付记录
            // $order_pay_log = OrderPayLog::query()
            //     ->whereOrderId($apply_refund->order_id)
            //     ->whereUserId($apply_refund->user_id)
            //     ->wherePayFrom(OrderPayLog::PAY_FROM_WECHAT)
            //     ->wherePayStatus(OrderPayLog::PAY_STATUS_SUCCESS)
            //     ->first();
            // if (!$order_pay_log) {
            //     throw new \Exception('退款失败，未查询到支付记录');
            // }
            //
            // // 执行退款
            // if ($sub_mch_id = $apply_refund->shop->sub_mch_id ?? '') {
            //     $wechat_res = WechatServicePaymentService::refundOrderMany($sub_mch_id, $order_pay_log->pay_sn, $apply_refund->flow_sn, $order_pay_log->money, $apply_refund->money);
            // } else {
            //     $wechat_res = WechatPaymentService::refundOrderMany($order_pay_log->pay_sn, $apply_refund->flow_sn, $order_pay_log->money, $apply_refund->money);
            // }
            //
            // if (isset($wechat_res['return_code']) && 'SUCCESS' === $wechat_res['return_code'] && isset($wechat_res['result_code']) && 'SUCCESS' === $wechat_res['result_code']) {
            //     //更新订单退款后的状态
            //     app(ApplyRefundDao::class)->changeOrderStatus($apply_refund);
            //     // 更改订单支付日志LOG
            //     $order_pay_log->refund_money += $apply_refund->money;
            //     if ($order_pay_log->refund_money >= $order_pay_log->money) {
            //         $order_pay_log->pay_status = OrderPayLog::PAY_STATUS_RETURN;
            //     }
            //     $order_pay_log->save();
            // } else {
            //     throw new \Exception('退款申请失败,请稍后重试~'.'订单ID：'.$order_id.'，商家：'.($apply_refund->shop->shop_name ?? ''));
            // }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // AliLogUtil::error('退款失败。申请退款状态：'.$status.'申请退款记录id:'.$apply_refund->id.$exception->getMessage());
        }
    }
}
