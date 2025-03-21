<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $app_website_decoration_id 网站装修ID
 * @property string $name 板块名称
 * @property string $component_name 组件名称
 * @property int $is_show 是否展示 1、展示 0、不展示
 * @property int $sort 排序
 * @property array<array-key, mixed> $content 组件内容 json格式
 * @property int $is_fixed_assembly 是否是固定组件 1是 0否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppWebsiteDecoration $app_website_decoration
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereAppWebsiteDecorationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereComponentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereIsFixedAssembly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppWebsiteDecorationItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AppWebsiteDecorationItem extends Model
{
    use HasFactory;
    /* 组件中的常量 */
    public const CHANNEL_TYPE_UP_DOWN = 1; // 频道广场-板块类型-商品涨跌
    public const CHANNEL_TYPE_PRICE_INDEX = 2; // 频道广场-板块类型- 价格指数
    public const CHANNEL_TYPE_CUSTOM = 3; // 频道广场-板块类型- 自定义板块
    public const CHANNEL_TITLE_STYLE_ICON = 1; // 频道广场-标题样式- 标题
    public const CHANNEL_TITLE_STYLE_ICON_TITLE = 2; // 频道广场-标题样式- ICON+标题
    public const CHANNEL_TITLE_STYLE_TITLE_LABEL = 3; // 频道广场-标题样式- 标题+标签
    public const GOODS_UP_DOWN_DATA_TYPE_DATE = 1; // 频道广场-商品涨跌- 按日期显示
    public const GOODS_UP_DOWN_DATE_NUM_TYPE = 2; // 频道广场-商品涨跌- 按数量显示

    public const DATA_SOURCE_PRICE_INDICES = 1; // 获取产品的价格指数
    public const DATA_SOURCE_INDEX_ITEMS = 2; // 获取多多指数表中数据
    /* 订单状态 */
    public const OS_UNCONFIRMED = 1; // 待确认
    public const OS_UNPAYED = 2; // 待付款
    public const OS_UNSHIPPED = 3; // 待发货
    public const OS_UNRECEIVED = 4; // 待收货
    public const OS_UNEVALUATE = 5; // 待评价
    public const OS_AFTERSALES = 6; // 退款/售后

    /* 价格指数类型枚举 */
    public const INDEX_PRICE_PVC = 'index_price_pvc'; // 价格指数:PVC
    public const INDEX_PRICE_ZIR = 'index_price_zir'; // 价格指数:ZIR
    public const INDEX_PRICE_TITANIUM = 'index_price_titanium'; // 价格指数:钛白粉
    public const INDEX_PRICE_INFO = 'index_price_info'; // 价格指数:除上述3个以外的数据
    public const INDEX_PRICE_CATEGORIES_ITEMS = 'index_price_categories_items'; // 价格指数:多多指数表中数据
    public const INDEX_NEW_ZIXUN = 'index_new_zixun'; // 价格指数:新版价格指数

    public const MINE_CUSTOM_STYLE_ONE = 1; // 我的-自定义板块--轮播样式-横向滚动
    public const MINE_CUSTOM_STYLE_TWO = 2; // 我的-自定义板块--轮播样式整屏翻页

    // 首页：标签栏
    public const COMPONENT_NAME_LABEL = 'label';
    // 首页：导航栏组件
    public const COMPONENT_NAME_HOME_NAV = 'home_nav';
    public const GOODS_HEADER = 'goods_header'; // 商品详情头部组件
    public const GOODS_FIXED = 'goods_fixed'; // 商品详情固定组件
    public const GOODS_MOVE = 'goods_move'; // 商品详情移动组件

    // 大屏广告位
    public const COMPONENT_NAME_LARGE_SCREEN = 'large_screen';
    // 签到送红包组件
    public const COMPONENT_NAME_RED_ENVELOPE = 'red_envelope';
    // 侧边广告位组件
    public const COMPONENT_NAME_SIDE_ADVERTISING = 'side_advertising';
    // 二楼广告位组件
    public const COMPONENT_NAME_SECOND_ADVERTISEMENT = 'second_advertisement';

    // 新闻
    public const COMPONENT_NAME_NEWS = 'news';
    // 推荐分类
    public const COMPONENT_NAME_RECOMMEND_CATE = 'recommend_cate';
    // 热力榜
    public const COMPONENT_NAME_HOT_LIST = 'hot_list';
    // 限时抢购
    public const COMPONENT_NAME_FLASH_SALE = 'flash_sale';

    // 金刚区
    public const COMPONENT_NAME_QUICK_LINK = 'quick_link';
    // 品牌精选
    public const COMPONENT_NAME_BRAND_CHOICE = 'brand_choice';
    // 广告位1
    public const COMPONENT_NAME_ADVERTISING_ONE = 'advertising_one';
    // 广告位2
    public const COMPONENT_NAME_ADVERTISING_TWO = 'advertising_two';
    // 广告位3
    public const COMPONENT_NAME_ADVERTISING_THREE = 'advertising_three';
    // 主题广告
    public const COMPONENT_NAME_THEME_ADVERTISING = 'theme_advertising';
    // 分类
    public const COMPONENT_NAME_CATEGORY = 'category';
    // 分类页面合集
    public const COMPONENT_NAME_CATEGORY_LIST = 'category_list';

    // 公共组件：频道广场
    public const COMPONENT_NAME_CHANNEL_SQUARE = 'channel_square';

    // 我的：订单中心
    public const COMPONENT_NAME_ORDER_CENTER = 'order_center'; // 固定组件
    // 我的：资产中心
    public const COMPONENT_NAME_MY_ASSET = 'my_asset'; // 资产中心
    // 我的：自定义板块
    public const COMPONENT_NAME_MINE_CUSTOM = 'mine_custom';

    // 公共组件：常买常逛
    public const COMPONENT_NAME_BUY_AND_SELL = 'buy_and_sell'; // 非固定组件
    // 公共组件：为您推荐-标题居左
    public const COMPONENT_NAME_RECOMMEND_LEFT = 'recommend_left'; // 非固定组件
    // 为您推荐-标题+主题
    public const COMPONENT_NAME_RECOMMEND_THEME = 'recommend_theme'; // 非固定组件
    // 为您推荐-店铺内推荐
    public const COMPONENT_NAME_RECOMMEND_SHOP = 'recommend_shop'; // 非固定组件
    // 为您推荐-标题居中
    public const COMPONENT_NAME_RECOMMEND_CENTER = 'recommend_center'; // 非固定组件
    // 为您推荐-推荐分类
    public const COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER = 'recommend_cat_or_center'; // 非固定组件
    // 推荐店铺
    public const COMPONENT_NAME_RECOMMEND_SELLER = 'recommend_seller';

    // 推荐产品
    public const COMPONENT_NAME_ZIXUN_PRODUCT = 'zixun_product';
    // 行情指数
    public const COMPONENT_NAME_ZIXUN_INDEX = 'zixun_index';
    // 宏观/期货
    public const COMPONENT_NAME_ZIXUN_MACRO_FUTURES = 'zixun_macro_futures';
    // 行业专家
    public const COMPONENT_NAME_COLLEGE_EXPERT = 'college_expert';
    // 视频中心
    public const COMPONENT_NAME_VIDEO = 'video';
    // 会议会展
    public const COMPONENT_NAME_MEETING = 'meeting';
    // 功能直达
    public const COMPONENT_NAME_DIRECT = 'component_name_direct';

    // 移动版-卖家中心组件
    public const COMPONENT_NAME_SELLER_SHOP_INFO = 'seller_shop_info'; // 店铺信息
    public const COMPONENT_NAME_SELLER_GOODS_DATA = 'seller_goods_data'; // 商品数据
    public const COMPONENT_NAME_SELLER_ORDER_DATA = 'seller_order_data'; // 订单数据
    public const COMPONENT_NAME_SELLER_BUSINESS_DATA = 'seller_business_data'; // 经营数据
    public const COMPONENT_NAME_SELLER_ADVERTISING_ONE = 'seller_advertising_one'; // 广告1
    public const COMPONENT_NAME_SELLER_HELP_CENTER = 'seller_help_center'; // 帮助中心
    public const COMPONENT_NAME_SELLER_LABEL = 'seller_label'; // 标签栏
    public const COMPONENT_NAME_SELLER_STORE_NAV = 'seller_store_nav'; // 店铺导航

    // 移动端-试用中心组件
    public const COMPONENT_NAME_TRY_NOTICE = 'try_notice'; // 公告
    public const COMPONENT_NAME_TRY_TITLE = 'try_title'; // 标题
    public const COMPONENT_NAME_TRY_LABEL = 'try_label'; // 标签栏
    public const COMPONENT_NAME_TRY_FREE_GOODS = 'try_free_goods'; // 试样商品
    public const COMPONENT_NAME_TRY_CHARGE_GOODS = 'try_charge_goods'; // 试用商品

    // 试样商品-页面布局
    public const TRY_DATA_TYPE_ONE = 1; // 1行1个
    public const TRY_DATA_TYPE_TWO = 2; // 1行2个

    // 公共组件：热销商品
    public const COMPONENT_NAME_HOT_SALE_GOOD = 'hot_sale_good';

    // 金刚区 && 推荐分类
    public const HORIZONTAL_SCROLLING = '1'; // 横向滚动
    public const FULL_SCREEN_FLIPPING = '2'; // 整屏翻页
    public const PLATE_HEIGHT_ZERO = '0'; // 板块高度不限制
    public const PLATE_HEIGHT_ONE = '1'; // 板块高度一行
    public const PLATE_HEIGHT_TWO = '2'; // 板块高度二行
    public const PLATE_HEIGHT_THREE = '3'; // 板块高度三行
    public const NUMBER_ROWS_THREE = '1'; // 每行个数3
    public const NUMBER_ROWS_FOUR = '2'; // 每行个数4
    public const NUMBER_ROWS_FIVE = '3'; // 每行个数5
    public const MORE_DEFAULT_TYPE = '1'; // 默认样式
    public const MORE_CUSTOM_PAGE = '2'; // 自定义页面
    // 行情指数
    public const INDEX_GOOD = '1'; // 商品涨跌
    public const INDEX_INDEX = '2'; // 多多指数
    public const INDEX_INDEX_PRODUCT = '1'; // 获取各产品的价格指数
    public const INDEX_INDEX_INDEX = '2'; // 获取“多多指数”表中数据

    // 新闻组件
    public const ONE_PER_PAGE = '1'; // 轮播样式-1条每页
    public const TWO_PER_PAGE = '2'; // 轮播样式-2条每页
    public const THREE_PER_PAGE = '3'; // 轮播样式-3条每页
    // 广告位展示样式
    public const AD_SHOW_STYLE_TILE = 1; // 展示样式-平铺展示
    public const AD_SHOW_STYLE_CAROUSEL = 2; // 展示样式-滚动轮播
    // 新闻展示样式
    public const NEW_SHOW_STYLE_LIST = 1; // 展示样式-静态列表
    public const NEW_SHOW_STYLE_CAROUSEL = 2; // 展示样式-滚动轮播

    // 资讯组件
    public const COMPONENT_NAME_ZIXUN_QUICK_LINK = 'zixun_quick_link'; // 资讯移动版金刚区
    public const COMPONENT_NAME_ZIXUN_APP_INDEX = 'zixun_app_index'; // 资讯移动版行情指数
    public const COMPONENT_NAME_ZIXUN_VIDEO = 'zixun_video'; // 资讯移动版首页视频
    public const COMPONENT_NAME_ZIXUN_ARTICLE = 'zixun_article'; // 资讯移动版首页文章
    public const COMPONENT_NAME_ZIXUN_LABEL = 'zixun_label'; // 资讯移动版底部标签
    public const COMPONENT_NAME_PUBLIC_IMAGE = 'public_image'; //图片样式（资讯|公共）

    // 订单状态说明
    public static $order_desc = [
        self::OS_UNCONFIRMED => '待确认',
        self::OS_UNPAYED => '待付款',
        self::OS_UNSHIPPED => '待发货',
        self::OS_UNRECEIVED => '待收货',
        self::OS_UNEVALUATE => '待评价',
        self::OS_AFTERSALES => '退款/售后',
    ];

    public static $numbers_rows = [
        self::NUMBER_ROWS_THREE => '3',
        self::NUMBER_ROWS_FOUR => '4',
        self::NUMBER_ROWS_FIVE => '5',
    ];

    // 组件名称
    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
    ];

    public function app_website_decoration()
    {
        return $this->belongsTo(AppWebsiteDecoration::class, 'app_website_decoration_id', 'id');
    }
}
