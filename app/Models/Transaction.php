<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $transaction_no   支付流水号
 * @property int         $user_id          用户ID
 * @property int         $parent_id        父交易ID
 * @property string      $transaction_type 交易类型:pay:支付,refund:退款
 * @property string      $type             交易业务类型
 * @property int         $type_id          交易业务ID
 * @property int         $payment_id       支付方式ID
 * @property numeric     $amount           支付金额
 * @property string      $remark           支付备注
 * @property int         $status           状态:0待处理,1处理成功
 * @property string|null $paid_at          支付完成时间
 * @property bool        $can_refund       是否允许退款
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Transaction> $children
 * @property-read int|null $children_count
 * @property-read Transaction|null $parent
 * @property-read Payment|null $payment
 * @property-read Model|\Eloquent $typeInfo
 * @property-read User $user
 *
 * @method static Builder<static>|Transaction newModelQuery()
 * @method static Builder<static>|Transaction newQuery()
 * @method static Builder<static>|Transaction query()
 * @method static Builder<static>|Transaction whereAmount($value)
 * @method static Builder<static>|Transaction whereCanRefund($value)
 * @method static Builder<static>|Transaction whereCreatedAt($value)
 * @method static Builder<static>|Transaction whereId($value)
 * @method static Builder<static>|Transaction wherePaidAt($value)
 * @method static Builder<static>|Transaction whereParentId($value)
 * @method static Builder<static>|Transaction wherePaymentId($value)
 * @method static Builder<static>|Transaction whereRemark($value)
 * @method static Builder<static>|Transaction whereStatus($value)
 * @method static Builder<static>|Transaction whereTransactionNo($value)
 * @method static Builder<static>|Transaction whereTransactionType($value)
 * @method static Builder<static>|Transaction whereType($value)
 * @method static Builder<static>|Transaction whereTypeId($value)
 * @method static Builder<static>|Transaction whereUpdatedAt($value)
 * @method static Builder<static>|Transaction whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Transaction extends BaseModel
{

    public const TRANSACTION_TYPE_PAY = 'pay'; // 支付
    public const TRANSACTION_TYPE_REFUND = 'refund'; // 退款
    public const STATUS_WAIT = 0; // 待处理
    public const STATUS_SUCCESS = 1; // 处理成功

    protected $guarded = [];

    public function typeInfo(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'type', 'type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'can_refund' => 'boolean',
        ];
    }
}
