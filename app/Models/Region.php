<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int|null $parent_id 父级ID
 * @property int $code 行政区划代码
 * @property string $name 区域名称
 * @property int $type 区域类型 1、省 2、市 3、区
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Region> $allChildren
 * @property-read int|null $all_children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Region> $children
 * @property-read int|null $children_count
 * @property-read Region|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Region extends Model
{
    public const ENABLE = 1; // 启用

    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren()
    {
        return $this->hasMany(Region::class, 'parent_id', 'id')->select(['id', 'name', 'parent_id', 'type', 'code'])->with('allChildren');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id', 'id')->select(['id', 'parent_id', 'id as value', 'name as text', 'name as label', 'code']);
    }
}
