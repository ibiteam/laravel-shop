<?php

namespace App\Models;




/**
 *
 *
 * @property int $id
 * @property int $app_decoration_id 装修页面ID
 * @property string $name 板块名称
 * @property string $component_name 组件名称
 * @property int $is_show 是否展示 1、展示 0、不展示
 * @property int $sort 排序
 * @property string $content 组件内容 json格式
 * @property int $is_fixed_assembly 是否是固定组件 1是 0否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereAppDecorationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereComponentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereIsFixedAssembly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereUpdatedAt($value)
 * @property-read \App\Models\AppWebsiteDecoration $app_website_decoration
 * @mixin \Eloquent
 */
class AppDecorationItem extends BaseModel
{


    protected $guarded = [];

    public const LONG_TIME = 1; // 时间 - 长期
    public const CUSTOM_TIME = 0; // 时间 - 自定义

    // 轮播图
    public const COMPONENT_NAME_HORIZONTAL_CAROUSEL = 'horizontal_carousel'; // 轮播图
    public const STYLE_TILED = 1; // 显示样式 - 平铺
    public const STYLE_TRANSITION = 2; // 显示样式 - 过渡
    public const COMPONENT_NAME_DANPING_ADVERTISEMENT = 'danping_advertisement'; // 弹屏广告
    public const COMPONENT_NAME_SUSPENDED_ADVERTISEMENT = 'suspended_advertisement'; // 悬浮广告
    public const COMPONENT_NAME_QUICK_LINK = 'quick_link'; // 金刚区
    public const PLATE_HEIGHT_ONE = 1; // 板块高度一行
    public const PLATE_HEIGHT_TWO = 2; // 板块高度二行
    public const PLATE_HEIGHT_THREE = 3; // 板块高度三行
    public const NUMBER_ROWS_THREE = 3; // 每行个数3
    public const NUMBER_ROWS_FOUR = 4; // 每行个数4
    public const NUMBER_ROWS_FIVE = 5; // 每行个数5
    public const COMPONENT_NAME_ADVERTISING_BANNER = 'advertising_banner'; // 广告位
    public const NUMBER_COLUMN_TWO = 2; // 每行显示两个
    public const NUMBER_COLUMN_THREE = 3; // 每行显示三个
    public const NUMBER_COLUMN_FOUR = 4; // 每行显示四个
    public const BACKGROUND_COLOR_SHOW = 1; // 展示背景色
    public const BACKGROUND_COLOR_NOT_SHOW = 0; // 不展示背景色
    public const COMPONENT_NAME_HOT_ZONE = 'hot_zone'; // 热区
    public const COMPONENT_NAME_HOME_NAV = 'home_nav'; // 导航搜索
    public const COMPONENT_NAME_LABEL = 'label'; // 底部标签栏
    public const COMPONENT_NAME_GOODS_RECOMMEND = 'goods_recommend'; // 商品推荐
    public const LAYOUT_ONE = 1; // 单列布局
    public const LAYOUT_TWO = 2; // 双列布局
    public const LAYOUT_THREE = 3; // 三列布局
    public const ALIGN_LEFT = 'left'; // 左对齐
    public const ALIGN_CENTER = 'center'; // 居中对齐
    // 推荐规则
    public const RULE_INTELLIGENT = 1; // 智能推荐
    public const RULE_MANUAL = 2; // 手动推荐
    // 排序类型
    public const SORT_SALES = 1; // 销量优先
    public const SORT_HIGH_PRAISE = 2; // 好评优先
    public const SORT_LOW_PRICE = 3; // 低价优先
    public const SORT_NEW_PRODUCT = 4; // 新品优先
    public const COMPONENT_NAME_RECOMMEND = 'recommend'; // 为您推荐

    protected $casts = [
        'content' => 'array',
    ];

    public function app_website_decoration()
    {
        return $this->belongsTo(AppWebsiteDecoration::class, 'app_decoration_id', 'id');
    }
}
