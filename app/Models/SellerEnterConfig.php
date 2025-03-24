<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $name           名称
 * @property string                          $type           组件类型
 * @property int                             $sort           排序，越大越靠前
 * @property int                             $is_need        是否必须
 * @property int                             $is_show        是否显示
 * @property string|null                     $limit_type     限制类型
 * @property int                             $limit_number   限制数量
 * @property string|null                     $select_options 下拉选
 * @property string|null                     $template_name  模板名称
 * @property string|null                     $template_url   模板地址
 * @property string|null                     $tips           提示文字
 * @property int|null                        $style_type     展示样式
 * @property int|null                        $style          样式效果
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereIsNeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereLimitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereSelectOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereStyleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereTemplateUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerEnterConfig whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SellerEnterConfig extends Model
{
    use DatetimeTrait;

    // 组件类型
    public const TYPE_TEXT = 'text';  // 文本框
    public const TYPE_TEXTAREA = 'textarea'; // 多行文本
    public const TYPE_RADIO = 'radio';  // 单选框
    public const TYPE_CHECKBOX = 'checkbox'; // 多选框
    public const TYPE_SELECT = 'select'; // 下拉框
    public const TYPE_DATE = 'date'; // 时间
    public const TYPE_FILE = 'file';  // 单文件
    public const TYPE_MORE_FILE = 'more_file'; // 多文件

    // 是否显示
    public const IS_SHOW_YES = 1;
    public const IS_SHOW_NO = 0;

    // 是否必须
    public const IS_NEED_YES = 1;
    public const IS_NEED_NO = 0;

    protected $guarded = [];
}
