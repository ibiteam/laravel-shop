<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property string                          $keywords      关键字
 * @property int                             $count         搜索数量
 * @property int                             $is_can_search 是否可查询
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereIsCanSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchKeyword whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SearchKeyword extends BaseModel
{

    public const CAN_SEARCH_YES = 1;    // 搜索可查询
    public const CAN_SEARCH_NO = 0;     // 搜索不可查询

    protected $guarded = [];
}
