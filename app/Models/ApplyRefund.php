<?php

namespace App\Models;

use App\Enums\ApplyRefundStatusEnum;
use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int                             $id
 * @property string                          $no              售后编号
 * @property int                             $user_id         用户ID
 * @property int                             $order_id        订单id
 * @property int                             $order_detail_id 订单明细id
 * @property int                             $type            类型
 * @property int                             $status          售后状态
 * @property string                          $money           退款金额
 * @property string                          $number          退款数量
 * @property int                             $reason_id       退款原因表ID
 * @property string|null                     $description     补充描述
 * @property array                           $certificate     退款凭证,号分割
 * @property int                             $is_revoke       是否撤销：0否 1是
 * @property int                             $count           申请次数
 * @property int                             $transaction_id  交易流水ID
 * @property string|null                     $result          结果
 * @property string|null                     $job_time        定时任务执行时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplyRefundLog> $applyRefundLogs
 * @property-read int|null $apply_refund_logs_count
 * @property-read \App\Models\ApplyRefundReason|null $applyRefundReason
 * @property-read \App\Models\ApplyRefundShip|null $applyRefundShip
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\OrderDetail|null $orderDetail
 * @property-read \App\Models\Transaction|null $transaction
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereIsRevoke($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereJobTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereOrderDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefund whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ApplyRefund extends Model
{
    use DatetimeTrait;

    // 类型 0退款；1退货退款
    public const TYPE_REFUND_MONEY = 0;
    public const TYPE_REFUND_GOODS = 1;

    // 是否撤销
    public const REVOKE_NO = 0; // 未撤销
    public const REVOKE_YES = 1; // 已撤销

    /**
     * 进行中的申请售后状态汇总 map.
     */
    public static array $statusInProgressMap = [
        ApplyRefundStatusEnum::NOT_PROCESSED->value,
        ApplyRefundStatusEnum::REFUSE->value,
        ApplyRefundStatusEnum::REFUSE_EXAMINE->value,
        ApplyRefundStatusEnum::BUYER_SEND_SHIP->value,
        ApplyRefundStatusEnum::SELLER_RECEIPT->value,
    ];

    protected $guarded = [];

    public function getCertificateAttribute($value): array
    {
        return $value ? explode(',', $value) : [];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function applyRefundLogs(): HasMany
    {
        return $this->hasMany(ApplyRefundLog::class, 'apply_refund_id', 'id')->orderByDesc('id');
    }

    public function applyRefundReason(): BelongsTo
    {
        return $this->belongsTo(ApplyRefundReason::class, 'reason_id', 'id');
    }

    public function applyRefundShip(): HasOne
    {
        return $this->hasOne(ApplyRefundShip::class, 'apply_refund_id', 'id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}
