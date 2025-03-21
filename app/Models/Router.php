<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name 名称
 * @property string|null $alias 别名
 * @property string $is_show 是否显示
 * @property string|null $url pc地址
 * @property string|null $h5_url h5地址
 * @property string|null $app_url app地址
 * @property string|null $mini_url 小程序地址
 * @property string|null $harmony_url 鸿蒙跳转地址
 * @property string|null $params 参数
 * @property string $scan_need_login 扫码的页面是否需要登录
 * @property string|null $hd_image_url 分享基础缩略图
 * @property string $android_is_open 安卓是否开启
 * @property string $ios_is_open ios是否开启
 * @property string $harmony_is_open 鸿蒙是否开启
 * @property string $mini_is_open 小程序是否开启
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereAndroidIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereAppUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereH5Url($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereHarmonyIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereHarmonyUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereHdImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereIosIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereMiniIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereMiniUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereScanNeedLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereUrl($value)
 * @mixin \Eloquent
 */
class Router extends Model
{
    public const CACHE_APP_ROUTER_LIST = 'cache_app_router_list'; // APP路由列表缓存
    public const HTTPS = 'https';  // HTTPS
    public const IS_SHOW = 1;
    public const NOT_IS_SHOW = 0;
    public const HOME = 'home';  // 首页
    public const MY_ASSET = 'my_asset';  // 我的资产
    public const COM_CERT = 'com_cert';  // 企业认证
    public const MY_ORDER = 'my_order';  // 我的订单(主页）
    public const NO_HOME_MY_ORDER = 'no_home_my_order';  // 我的订单（非主页）
    public const TRY = 'try';  // 试用
    public const SPECIAL = 'special';  // 专场
    public const ARTICLE_INDEX = 'article_index';  // 文章分类
    public const ARTICLE_DETAIL = 'article_detail';  // 文章详情
    public const ZIXUN_ARTICLE_INDEX = 'zixun_article_index';  // 资讯文章分类
    public const ZIXUN_ARTICLE_DETAIL = 'zixun_article_detail';  // 资讯文章详情
    public const ARTICLE_INDUSTRY_SUMMIT = 'article_industry_summit';  // 会议中心+多多学院
    public const GOODS = 'goods';  // 商品详情
    public const STORE = 'store';  // 店铺首页
    public const MARKET = 'market';  // 超市
    public const CROP = 'crop';  // 集市
    public const SEARCH = 'search';  // 搜索页
    public const SHOP_STREET = 'shop_street';  // 店铺街
    public const GROUP = 'group';  // 拼团
    public const INTEGRAL_SHOP = 'integral_shop';  // 积分商城
    public const INTEGRAL_SUPPLY = 'integral_supply';  // 积分分类下商品
    public const INTEGRAL_GOODS = 'integral_goods'; // 积分商品详情
    public const SHIP = 'ship';  // 物流
    public const MY = 'my';  // 我的
    public const FIND_CAR = 'find_car';  // 我要找车
    public const LIVE_SQUARE = 'live_square';  // 直播广场
    public const LIVE_SHOW = 'live_show';  // 直播观看端
    public const LIVE_NOTICE = 'live_notice';  // 直播预告
    public const LIVE_RECORD = 'live_record';  // 直播回放
    public const BARGAIN = 'bargain';  // 砍价
    public const OEM = 'oem';  // OEM
    public const EQUIPMENT = 'equipment';  // 机械设备
    public const INTEGRAL_LIST = 'integral_list';  // 积分明细
    public const SIGNIN = 'signIn';  // 签到
    public const CATEGORY = 'category';  // 分类（主页）
    public const NO_HOME_CATEGORY = 'no_home_category';  // 分类(非主页）
    public const CATEGORY_PUBLIC = 'category_public';  // 分类(公共页面）
    public const CART = 'cart';  // 购物车从
    public const PROMOTE = 'promote';  // 主页分销中心
    public const NO_HOME_PROMOTE = 'no_home_promote';  // 非主页分销中心
    public const SERVER_ENTER = 'server_enter';  // 服务商入驻
    public const SERVER_ORDER = 'server_order';  // 归集订单
    public const HELPCENTER = 'help_certer';  // 帮助中心
    public const MYAUCTION = 'myauction';  // 我的竞拍
    public const MY_INQUIRY_PRICE = 'my_inquiry_price';  // 我的询单
    public const MY_QUOTE_PRICE = 'my_quote_price';  // 我的报价单
    public const MY_INQUIRY_SUPPLY = 'my_inquiry_supply';  // 我的供应
    public const INQUIRY_LIST = 'inquiry_list';  // 询单管理
    public const CONTRACT = 'contract';  // 电子合同
    public const DELIVERY_LIST = 'delivery_list';  // 发货管理
    public const INVOICE_MAKEOUT = 'invoice_makeout';  // 申请开票
    public const INVOICE = 'invoice';  // 发票管理
    public const ADDRESS = 'address';  // 收获地址
    public const AWARDS = 'awards';  // 我的奖励
    public const ATTENTION = 'attention';  // 我的关注
    public const MYOEM = 'myoem';  // oem管理
    public const EQUIPMENT_ADMIN = 'equipment_admin';  // 机械设备管理
    public const INTEGRAL_EXCHANGE = 'integral_exchange';  // 我能兑换 积分明细中的立即兑换
    public const MYEVALUATE = 'myEvaluate';  // 待评价
    public const ORDERDETAIL = 'myOrder_detail';  // 订单详情
    public const COUPON = 'coupon';  // 优惠券
    public const REDPACKET = 'redPacket';  // 红包
    public const ORDER_CHECKOUT = 'order_checkout';  // 填写订单
    public const PAY = 'pay';  // 去支付
    public const UPDATEPHONE = 'updatePhone';  // 修改手机号
    public const GETPASSWORD = 'getPassword';  // 修改密码
    public const ACCOUNTSECURITY = 'accountSecurity';  // 账户安全
    public const FEEDBACK = 'feedback';  // 意见反馈
    public const USERINFO = 'userInfo';  // 个人信息
    public const ACCOUNT = 'account';  // 账户设置
    public const PROMOTE_TEMPLATE = 'live/publicity'; // 活动宣传
    public const ACTIVITY = 'activity'; // 活动模板
    public const SHIP_INDEX = 'ship_index'; // 查看物流
    public const MYEVALUATE_PUBLISH = 'myevaluate_publish'; // 发表评价
    public const MYEVALUATE_LIST = 'myevaluate_list'; // 我的评价

    public const DOUBLE_TEN_BROWSE = 'double_ten_browse'; // 非主页活动模板
    public const HOME_DOUBLE_TEN_BROWSE = 'home_double_ten_browse'; // 主页活动模板
    public const HOT = 'hot'; // 热力榜
    public const PRESELL_INDEX = 'presell_index'; // 预售列表
    public const PRESELL_DETAIL = 'presell_detail'; // 预售详情
    public const PROBATION_INDEX = 'probation_index'; // 多多试用列表
    public const PROBATION_GOODS = 'probation_goods'; // 多多试用商品详情

    public const VIEW_GOODS = 'view_goods'; // 浏览商品
    public const VIEW_SHOP = 'view_shop'; // 浏览店铺

    public const AUCTION_DETAIL = 'auction_detail'; // 竞拍详情
    public const AUCTION_CENTER = 'auctionCenter'; // 竞拍
    public const AUCTION_LIST = 'auction_list'; // 竞拍列表(汇总竞拍)

    public const DELIVER_APPLY = 'deliver_apply'; // 发货申请
    public const DEALER_MANAGE = 'dealer_manage'; // 经销商管理

    public const NEW_YEAR_MARKING = 'new_year_marking'; // 新年营销
    public const NEW_YEAR_WELFARE = 'new_year_welfare'; // 新年红包
    public const HOME_APP_MESSAGE = 'home_app_message'; // 消息
    public const NO_HOME_APP_MESSAGE = 'no_home_app_message'; // 非主页消息

    public const INTENTION_ORDER = 'intention_order';  // 意向订单


    public const QUESTIONNAIRE_SURVEY = 'questionnaire_survey'; // 问卷调查

    public const JICAI_SECKILL_INDEX = 'jicai_seckill_index'; // 集采首页
    public const JICAI_SECKILL_ORDER = 'jicai_seckill_order'; // 集采订单列表
    public const QUOTATION_INDEX = 'quotation_index'; // 报价单列表

    public const AFTER_SALES = 'after_sales';  // 退款/售后
    public const ZIXUN_INDEX = 'zixun_index';  // 指数(主页）
    public const NO_HOME_ZIXUN_INDEX = 'no_home_zixun_index';  // 指数 (非主页）
    public const PUBLIC_PAGE = 'public_page';  // 公共页面(主页）
    public const NO_HOME_PUBLIC_PAGE = 'no_home_public_page';  // 公共页面 (非主页）
    public const INDUSTRY = 'industry';  // 产业链专题 (合集）
    public const DELETED_ORDER = 'deleted_order';  // 已删除订单
    public const SHARE_APP = 'share_app';  // 分享app
    public const CUSTOMER_SERVICE = 'customer_service';  // 官方客服
    public const LIVE_MANAGE = 'live_manage';  // 直播管理
    public const INVITE_PRIZE = 'invite_prize';  // 邀请有奖
    public const APPLY_ENTER = 'apply_enter';  // 商家入驻
    public const DEALERS_ADDRESS = 'dealers_address';  // 经销商配送地址
    public const INVITE_TEMPLATE = 'invite_template';  // 邀约模板
    public const CASHDELIVERY_ORDER = 'cashdelivery_order';  // 现货交割单
    public const VR_INDEX = 'vr_index';  // vr云工厂
    public const WULIU_INDEX = 'wuliu_index';  // 国联智运平团
    public const INDUSTRY_MAP = 'industry_map';  // 产业链地图
    public const CHAIN_INDEX = 'chain_index';  // 产业链
    public const PROMOTE_SHARE = 'promote_share';  // 多多精选（分销分享商品列表）
    public const ZIXUN_SILICON_FUTURES = 'zixun_silicon_futures';  // 期货
    public const GIFT_CARD_CENTER = 'gift_card_center'; // 兑换中心(提货中心）
    public const YI_QI_XIU = 'yi_qi_xiu'; // 易企秀

    public const ZIXUN_APP_HOME = 'zixun_app_home';  // 资讯移动端首页
    public const ZIXUN_APP_CATEGORY_PAGE = 'zixun_app_category_page';  // 资讯移动端分类单页
    public const COLLECT_SUBSCRIBE = 'collect_subscribe';  // 关注订阅号
    public const ZIXUN_GOOD_CHANGE = 'zixun_good_change';  // 商品涨跌
    public const ZOXUN_EXPONENT_TINDEX = 'zoxun_exponent_tindex';  // 价格指数
    public const POINT_TRADING = 'point_trading';  // 点价交易
    protected $guarded = [];

    //鸿蒙不显示的广告
    public static $harmony_no_show = [
        self::SHARE_APP,
        self::QUOTATION_INDEX,
        self::VR_INDEX,
        self::APPLY_ENTER,
        self::WULIU_INDEX,
    ];
}
