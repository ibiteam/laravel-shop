<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $user_id       会员id
 * @property string                          $enter_info    入驻信息
 * @property string                          $source        来源
 * @property int                             $check_status  审核状态：0未审核，1审核通过，2审核不通过
 * @property int                             $admin_user_id 审核人
 * @property string|null                     $check_desc    审核说明
 * @property string|null                     $remark        备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereCheckDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereCheckStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereEnterInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnter whereUserId($value)
 *
 * @mixin \Eloquent
 */
class SellerEnter extends Model
{
    use DatetimeTrait;
    public const CHECK_NOT_YET = 0; // 还未审核
    public const CHECK_APPROVED = 1; // 审核通过
    public const CHECK_NOT_APPROVED = 2; // 审核未通过
    protected $guarded = [];
}
