<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $name       名称
 * @property string      $code       编码
 * @property bool        $status     状态:1启用 0禁用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|ShipCompany newModelQuery()
 * @method static Builder<static>|ShipCompany newQuery()
 * @method static Builder<static>|ShipCompany query()
 * @method static Builder<static>|ShipCompany whereCode($value)
 * @method static Builder<static>|ShipCompany whereCreatedAt($value)
 * @method static Builder<static>|ShipCompany whereId($value)
 * @method static Builder<static>|ShipCompany whereName($value)
 * @method static Builder<static>|ShipCompany whereStatus($value)
 * @method static Builder<static>|ShipCompany whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ShipCompany extends Model
{
    use DatetimeTrait;

    public const STATUS_ENABLE = 1;
    public const STATUS_DISABLE = 0;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
