<?php

namespace App\Models;

//use App\Utils\Route;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name 名称
 * @property array<array-key, mixed> $type 类型:1：底部菜单；2：一级页面；3：二级页面
 * @property int $is_show 是否显示
 * @property string $alias 页面别名
 * @property int $parent_id 父级集合id
 * @property int $admin_user_id 最后一次装修人ID/管理员ID
 * @property string $image_url 封面地址
 * @property string|null $title 网页TDK:标题
 * @property string|null $keywords 网页TDK:关键词
 * @property string|null $description 网页TDK:描述
 * @property int $version 版本：1买家版 2卖家版
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppWebsiteDecoration> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AppWebsiteDecorationItem> $item
 * @property-read int|null $item_count
 * @property-read AppWebsiteDecoration|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecoration whereVersion($value)
 * @mixin \Eloquent
 */
class AppWebsiteDecoration extends Model
{
    use HasFactory;

    /* 固定别名 */
    public const ALIAS_HOME = 'home'; // 首页
    public const ALIAS_INDEX = 'index'; // 指数
    public const ALIAS_CATEGORY = 'category'; // 分类
    public const ALIAS_DISTRIBUTION = 'distribution'; // 分销
    public const ALIAS_NOTICE = 'notice'; // 消息
    public const ALIAS_CART = 'cart'; // 购物车
    public const ALIAS_ORDER = 'order'; // 订单
    public const ALIAS_MINE = 'mine'; // 我的
    public const ALIAS_GOODS = 'goods'; // 商品页面
    public const ALIAS_INDUSTRIAL = 'industrial'; // 产业链专题
    public const ALIAS_PUBLIC = 'public'; // 公共页面
    public const ALIAS_CATEGORY_LIST = 'category_list'; // 分类页面
    public const ALIAS_SELLER_HOME = 'seller_home'; // 卖家版-首页
    public const ALIAS_SELLER_MESSAGE = 'seller_message'; // 卖家版-消息
    public const ALIAS_SELLER_WORKBENCH = 'seller_workbench'; // 卖家版-工作台
    public const ALIAS_TRY_CENTER = 'try_center'; // 买家版-试用中心
    public const ALIAS_ZIXUN_HOME_PAGE = 'zixun_home_page'; // 资讯首页
    public const ALIAS_ZIXUN_CATEGORY_PAGE = 'zixun_category_page'; // 资讯分类合集
    public const ALIAS_VR_FACTORY = 'vr_factory'; // VR云工厂
    public const ALIAS_LIVE_SQUARE = 'live_square'; // 直播广场
    public const VERSION_BUYER = 1; // 版本：买家版
    public const VERSION_SELLER = 2; // 版本：买家版
    public const TYPE_BOTTOM_MENU = 1; // 底部菜单
    public const TYPE_FIRST_LEVEL = 2; // 一级页面
    public const TYPE_SECONDARY = 3; // 二级页面

    /* cache start */
    public const MOBILE_HOME_BY_MINI = 'mobile_home_by_mini'; // 小程序首页缓存
    public const MOBILE_HOME_BY_M = 'mobile_home_by_m'; // M端首页缓存
    public const MOBILE_HOME_BY_APP = 'mobile_home_by_app'; // APP 首页缓存
    public const MOBILE_HOME_BY_HARMONY = 'mobile_home_by_harmony'; // 鸿蒙APP 首页缓存
    public const MOBILE_HOME_BY_COMMON = 'mobile_home_by_common'; // 公共首页缓存
    public const MOBILE_LABEL_BY_MINI = 'mobile_label_by_mini'; // 小程序标签栏缓存
    public const MOBILE_LABEL_BY_M = 'mobile_label_by_m'; // M端标签栏缓存
    public const MOBILE_LABEL_BY_APP = 'mobile_label_by_app'; // APP 标签栏缓存
    public const MOBILE_LABEL_BY_COMMON = 'mobile_label_by_common'; // 公共标签栏缓存

    // 产业链缓存标识
    public const APPLET_ALIAS_INDUSTRIAL = 'applet_industrial'; // 小程序标签栏缓存
    public const M_ALIAS_INDUSTRIAL = 'm_industrial'; // M端标签栏缓存
    public const APP_ALIAS_INDUSTRIAL = 'app_industrial'; // APP 标签栏缓存
    public const HARMONY_APP_INDUSTRIAL = 'harmony_app_industrial'; // 鸿蒙APP 标签栏缓存

    // 公共页面缓存标识
    public const APPLET_ALIAS_PUBLIC = 'applet_public'; // 小程序标签栏缓存
    public const M_ALIAS_PUBLIC = 'm_public'; // M端标签栏缓存
    public const APP_ALIAS_PUBLIC = 'app_public'; // APP 标签栏缓存
    public const HARMONY_APP_PUBLIC = 'harmony_app_public'; // 鸿蒙APP 标签栏缓存-

    // 商品装页面 cache key
    public const MOBILE_GOODS_CONFIG = 'mobile_goods_config';

    /* 首页缓存标识集合 以便清除缓存 */
    public static $homeCacheMapAlias = [
        self::MOBILE_HOME_BY_MINI,
        self::MOBILE_HOME_BY_M,
        self::MOBILE_HOME_BY_APP,
        self::MOBILE_HOME_BY_HARMONY,
        self::MOBILE_HOME_BY_COMMON,
        self::MOBILE_LABEL_BY_MINI,
        self::MOBILE_LABEL_BY_M,
        self::MOBILE_LABEL_BY_APP,
        self::MOBILE_LABEL_BY_COMMON,
    ];
    /* cache end */

    // 产业链页面缓存
    public static $industrialCacheMapAlias = [
        self::APPLET_ALIAS_INDUSTRIAL,
        self::M_ALIAS_INDUSTRIAL,
        self::APP_ALIAS_INDUSTRIAL,
        self::HARMONY_APP_INDUSTRIAL,
    ];

    public static $publicCacheMapAlias = [
        self::APPLET_ALIAS_PUBLIC,
        self::M_ALIAS_PUBLIC,
        self::APP_ALIAS_PUBLIC,
        self::HARMONY_APP_PUBLIC,
    ];

    /**
     * 允许新增页面.
     *
     * @var string[]
     */
    public static $storeMapAlias = [
        self::ALIAS_CATEGORY_LIST, self::ALIAS_INDUSTRIAL, self::ALIAS_PUBLIC, self::ALIAS_ZIXUN_CATEGORY_PAGE,
    ];

    /**
     * 不展示任何操作按钮的页面.
     *
     * @var string[]
     */
    public static $notShowBottomMapAlias = [
        self::ALIAS_CART, self::ALIAS_NOTICE, self::ALIAS_SELLER_MESSAGE,
    ];

    protected $guarded = [];

    protected $casts = [
        'type' => 'array',
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppWebsiteDecorationItem::class, 'app_website_decoration_id', 'id')->orderBy('sort');
    }

    public function adminUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * 获取文章分类页的地址
     */
//    public function getUrlAttribute()
//    {
//        $url = '';
//
//        if ($this->alias == self::ALIAS_HOME) {
//            $url = get_h5_url(Route::HOME);
//        } elseif ($this->alias == self::ALIAS_INDEX) {
//            $url = get_h5_url(Route::ZIXUN_INDEX);
//        } elseif ($this->alias == self::ALIAS_CATEGORY) {
//            $url = get_h5_url(Route::CATEGORY);
//        } elseif ($this->alias == self::ALIAS_DISTRIBUTION) {
//            $url = get_h5_url(Route::CATEGORY);
//        } elseif ($this->alias == self::ALIAS_CART) {
//            $url = get_h5_url(Route::CART);
//        } elseif ($this->alias == self::ALIAS_MINE) {
//            $url = get_h5_url(Route::UCENTER);
//        } elseif ($this->alias == self::ALIAS_ZIXUN_HOME_PAGE) {
//            $url = get_h5_url(Route::ZIXUN_APP_HOME);
//        } elseif ($this->alias == self::ALIAS_SELLER_HOME) {
//            $url = get_h5_url(Route::SELLER_INDEX);
//        } elseif ($this->alias == self::ALIAS_SELLER_WORKBENCH) {
//            $url = get_h5_url(Route::SELLER_WORKBENCH);
//        } elseif ($this->alias == self::ALIAS_TRY_CENTER) {
//            $url = get_h5_url(Route::TRY_CENTER);
//        } elseif ($this->alias == self::ALIAS_VR_FACTORY) {
//            $url = get_h5_url(Route::VR_INDEX);
//        }elseif ($this->alias == self::ALIAS_LIVE_SQUARE) {
//            $url = get_h5_url(Route::LIVE_SQUARE);
//        }
//
//        return $url;
//    }
}
