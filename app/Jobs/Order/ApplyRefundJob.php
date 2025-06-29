<?php

namespace App\Jobs\Order;

use App\Enums\ApplyRefundStatusEnum;
use App\Exceptions\BusinessException;
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
use Illuminate\Support\Facades\Log;

class ApplyRefundJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $status;             // 退款状态
    public int $apply_refund_id;    // 申请退款id
    public string $action;          // 操作行为
    public int $type;               // 类型：0买方；1卖方

    /**
     * Create a new job instance.
     */
    public function __construct($status, $apply_refund_id, $action, $type)
    {
        $this->onQueue('order');    // 指定任务队列

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
                    $this->executeRefund($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::REFUSE->value: // 状态: 已拒绝退款
                if ($apply_refund->status == ApplyRefundStatusEnum::REFUSE->value) {
                    $this->closeRefund($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::REFUSE_EXAMINE->value: // 状态: 退货审核成功 待买家发货
                if ($apply_refund->status == ApplyRefundStatusEnum::REFUSE_EXAMINE->value) {
                    $this->closeRefund($this->action, $this->type, $this->status);
                }

                break;

            case ApplyRefundStatusEnum::BUYER_SEND_SHIP->value:  // 状态: 买家已发货 待卖家收货
                if ($apply_refund->status == ApplyRefundStatusEnum::BUYER_SEND_SHIP->value) {
                    $this->executeRefund($this->action, $this->type, $this->status);
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
    public function closeRefund($action, $type, $status): void
    {
        $apply_refund = ApplyRefund::query()->with(['user'])->whereId($this->apply_refund_id)->first();

        $apply_refund->status = ApplyRefundStatusEnum::REFUND_CLOSE->value;

        DB::beginTransaction();

        try {
            $apply_refund->save();

            // 买家超时未申请，不记录日志
            if ($status === ApplyRefundStatusEnum::REFUSE_EXAMINE->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund, $apply_refund->user?->user_name, $action, $type);
            }
            DB::commit();
        } catch (BusinessException $business_exception) {
            Log::error('关闭退款队列错误~'.$business_exception->getMessage());
            DB::rollBack();
        } catch (\Throwable $throwable) {
            Log::error('关闭退款队列异常~'.$throwable->getMessage());
            DB::rollBack();
        }
    }

    /**
     * 执行退款.
     *
     * @throws \Throwable
     */
    public function executeRefund($action, $type, $status): void
    {
        $apply_refund = ApplyRefund::query()->with(['order', 'orderDetail', 'user', 'applyRefundReason'])->whereId($this->apply_refund_id)->first();

        $apply_refund->status = ApplyRefundStatusEnum::REFUND_SUCCESS->value;
        $apply_refund->result = '卖家超时未处理，退款成功';

        DB::beginTransaction();

        try {
            $apply_refund->save();

            // 发起申请售后 超时未处理
            if ($status === ApplyRefundStatusEnum::NOT_PROCESSED->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund, $apply_refund->user?->user_name, '卖家超时未处理，已退款给买家', ApplyRefundLog::TYPE_SELLER);
            }

            if ($status === ApplyRefundStatusEnum::BUYER_SEND_SHIP->value) {
                app(ApplyRefundLogDao::class)->addLog($apply_refund, $apply_refund->user?->user_name, '卖家超时未确认收货，已退款给买家', ApplyRefundLog::TYPE_SELLER);
            }

            // 操作退款
            app(ApplyRefundDao::class)->operationRefund($apply_refund);

            DB::commit();
        } catch (BusinessException $business_exception) {
            Log::error('执行退款队列错误~'.$business_exception->getMessage());
            DB::rollBack();
        } catch (\Throwable $throwable) {
            Log::error('执行退款队列异常~'.$throwable->getMessage());
            DB::rollBack();
        }
    }
}
