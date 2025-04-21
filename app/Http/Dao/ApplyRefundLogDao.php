<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use App\Models\User;

class ApplyRefundLogDao
{
    /**
     * 添加记录.
     */
    public function addLog($apply_refund_id, $action_name, $action, $type, $apply_refund_ship_id = 0)
    {
        return ApplyRefundLog::query()->create([
            'apply_refund_id' => $apply_refund_id,
            'action_name' => $action_name,
            'action' => $action,
            'type' => $type,
            'apply_refund_ship_id' => $apply_refund_ship_id,
        ]);
    }

    /**
     * 协商历史.
     *
     * @throws BusinessException
     */
    public function logList(User $user, int $apply_refund_id): array
    {
        $apply_refund = ApplyRefund::query()
            ->with(['orderDetail:id,goods_id,goods_unit', 'orderDetail.goods:id,unit', 'user'])
            ->whereUserId($user->id)->whereId($apply_refund_id)
            ->first();

        if (! $apply_refund instanceof ApplyRefund) {
            throw new BusinessException('退款信息不存在');
        }

        $temp_unit = $apply_refund->orderDetail?->goods_unit ?: $apply_refund->orderDetail?->goods?->unit;
        $temp_user = $apply_refund->user;

        return ApplyRefundLog::query()
            ->with(['applyRefund', 'applyRefundShip'])
            ->whereApplyRefundId($apply_refund->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (ApplyRefundLog $apply_refund_log) use ($temp_unit, $temp_user) {
                if ($apply_refund_log->type === ApplyRefundLog::TYPE_BUYER) {
                    $temp_name = $temp_user->user_name ?? '';
                    $temp_img = $temp_user->avatar ?? '';
                } else {
                    $temp_name = '卖家';
                    $temp_img = '';
                }
                $temp_apply_refund_shipping = null;

                if ($apply_refund_shipping = $apply_refund_log->applyRefundShip) {
                    $temp_apply_refund_shipping = [
                        'company' => $apply_refund_shipping->shipCompany?->name,
                        'no' => $apply_refund_shipping->no,
                        'phone' => $apply_refund_shipping->phone,
                        'certificate' => $apply_refund_shipping->certificate,
                        'description' => $apply_refund_shipping->description,
                    ];
                }

                return [
                    'img' => $temp_img,
                    'name' => $temp_name,
                    'created_at' => $apply_refund_log->created_at->format('Y-m-d H:i:s'),
                    'action' => $apply_refund_log->action,
                    'type' => $apply_refund_log->type,
                    'reason' => $apply_refund_log->applyRefund->applyRefundReason->content,
                    'refund_money' => $apply_refund_log->applyRefund->money,
                    'refund_number' => get_new_price($apply_refund_log->applyRefund->number),
                    'unit' => $temp_unit,
                    'certificate' => $apply_refund_log->applyRefund->certificate,
                    'description' => $apply_refund_log->applyRefund->description,
                    'refund_type' => $apply_refund_log->applyRefund->type,
                    'apply_refund_shipping' => $temp_apply_refund_shipping,
                ];
            })->toArray();
    }
}
