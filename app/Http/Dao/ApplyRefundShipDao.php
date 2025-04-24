<?php

namespace App\Http\Dao;

use App\Enums\ApplyRefundStatusEnum;
use App\Exceptions\BusinessException;
use App\Jobs\Order\ApplyRefundJob;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use App\Models\ApplyRefundShip;
use App\Models\ShopConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplyRefundShipDao
{
    /**
     * 填写退货物流.
     *
     * @throws BusinessException
     * @throws \Throwable
     */
    public function addShip(User $user, int $apply_refund_id, string $no, int $ship_company_id, string $phone, ?string $description = null, ?string $certificate = null)
    {
        $apply_refund = ApplyRefund::query()->with(['applyRefundShip'])->whereId($apply_refund_id)->whereUserId($user->id)->first();

        if (! $apply_refund instanceof ApplyRefund) {
            throw new BusinessException('退款信息不存在');
        }

        if ($apply_refund->applyRefundShip) {
            throw new BusinessException('您已填写物流信息');
        }

        DB::beginTransaction();

        try {
            $apply_refund_ship = ApplyRefundShip::query()->create([
                'apply_refund_id' => $apply_refund_id,
                'no' => $no,
                'ship_company_id' => $ship_company_id,
                'phone' => $phone,
                'description' => $description,
                'certificate' => $certificate,
            ]);

            $seller_receive_time = intval(shop_config(ShopConfig::SELLER_RECEIVE_TIME));

            $delayed_time = Carbon::now()->addDays($seller_receive_time);
            $apply_refund->job_time = $delayed_time;
            $apply_refund->status = ApplyRefundStatusEnum::BUYER_SEND_SHIP->value;
            $apply_refund->save();

            app(ApplyRefundLogDao::class)->addLog($apply_refund, $user->user_name, '买家提交了物流单号', ApplyRefundLog::TYPE_BUYER, $apply_refund_ship->id);

            DB::commit();

            ApplyRefundJob::dispatch(ApplyRefundStatusEnum::BUYER_SEND_SHIP->value, $apply_refund->id, '卖家超时未确认收货，已退款给买家', ApplyRefundLog::TYPE_BUYER)->delay($delayed_time);
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw new BusinessException('填写退货物流失败');
        }

        return $apply_refund_ship;
    }
}
