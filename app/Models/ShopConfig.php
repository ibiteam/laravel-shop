<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property string                          $group_name 分组名称
 * @property string                          $code       标识
 * @property array<array-key, mixed>|null    $value      值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereValue($value)
 *
 * @mixin \Eloquent
 */
class ShopConfig extends BaseModel
{


    /**
     * group组名.
     */
    public const GROUP_SITE_INFO = 'site_info'; // 站点信息
    public const GROUP_SITE_LOGO = 'site_logo'; // 站点Logo
    public const GROUP_SMTP_SERVICE = 'smtp_service'; // 邮件服务
    public const GROUP_MANAGE_SETTINGS = 'manage_settings'; // 后台设置
    public const GROUP_INTEGRAL = 'group_integral'; // 积分设置
    public const GROUP_SEARCH = 'group_search';     // 搜索设置
    public const GROUP_GOODS = 'group_goods'; // 商品设置
    public const GROUP_REFUND_AFTER_SALES = 'group_refund_after_sales'; // 退款售后
    public const GROUP_ARTICLES = 'group_articles'; // 文章设置

    /**
     * code值.
     */
    // 站点信息
    public const SHOP_NAME = 'shop_name'; // 商城名称
    public const SHOP_KEYWORDS = 'shop_keywords'; // 商城关键词
    public const SHOP_DESCRIPTION = 'shop_description'; // 商城描述
    public const BANK_ACCOUNT = 'bank_account'; // 银行账号
    public const SHOP_ADDRESS = 'shop_address'; // 商城线下地址
    public const SERVICE_MOBILE = 'service_mobile'; // 服务热线
    public const ICP_NUMBER = 'icp_number'; // ICP 备案号
    public const SHOP_COLOR = 'shop_color'; // 站点主题色
    public const MANAGE_COLOR = 'manage_color'; // 总后台主题色
    public const MOUSE_MOVE_COLOR = 'mouse_move_color'; // 鼠标移入背景色
    public const IS_GRAY = 'is_gray'; // 网站首页是否置灰

    // 站点Logo
    public const SHOP_ICON = 'shop_icon'; // 商城 ICON
    public const SHOP_LOGO = 'shop_logo'; // 商城 LOGO
    public const SHOP_MANAGE_LOGIN_IMAGE = 'shop_manage_login_image'; // 商城后台登录页面图片

    // 邮件服务
    public const SMTP_HOST = 'smtp_host'; // 发送邮件服务器地址(SMTP)
    public const SMTP_PORT = 'smtp_port'; // 服务器端口
    public const SMTP_USER = 'smtp_user'; // 邮件发送账号
    public const SMTP_PASS = 'smtp_pass'; // 账号密码

    // 后台设置
    public const MANAGE_LOGIN_RSA_PUBLIC_KEY = 'manage_login_rsa_public_key'; // 后台登录RSA公钥
    public const MANAGE_LOGIN_RSA_PRIVATE_KEY = 'manage_login_rsa_private_key'; // 后台登录RSA私钥

    // 积分设置
    public const IS_OPEN_INTEGRAL = 'is_open_integral'; // 是否开启积分
    public const INTEGRAL_NAME = 'integral_name'; // 积分名称

    // 搜索设置
    public const SEARCH_DRIVER = 'search_driver';   // 搜索方式(1-数据库,2-MeiliSearch)

    // 商品设置
    public const IS_SHOW_SALES_VOLUME = 'is_show_sales_volume'; // 是否显示销量
    public const IS_SHOW_AFTER_SALES = 'is_show_after_sales';    // 是否显示售后申请
    public const CURRENCY_FORMAT = 'currency_format'; // 对价格进行格式化
    public const PRICE_FORMAT = 'price_format'; // 保留几位小数

    // 退款售后
    public const SELLER_SHIPPED_TIME = 'seller_shipped_time'; // 卖家未发货退款申请响应时间（小时）
    public const SELLER_CONFIRM_TIME = 'seller_confirm_time'; // 卖家处理响应时间（小时）
    public const BUYER_CHANGE_TIME = 'buyer_change_time'; // 买家修改申请响应时间（小时）
    public const BUYER_REFUND_TIME = 'buyer_refund_time'; // 买家退货响应时间（天）
    public const SELLER_RECEIVE_TIME = 'seller_receive_time'; // 卖家收货响应时间（天）
    public const AFTER_SALES_TIMELINESS = 'after_sales_timeliness'; // 售后时效（天）
    public const AFTER_SALES_MAX_MONEY = 'after_sales_max_money'; // 售后最大退款金额

    // 文章设置
    public const USER_AGREEMENT = 'user_agreement'; // 用户协议
    public const USER_CANCEL_AGREEMENT = 'user_cancel_agreement'; // 用户注销协议
    public const PRIVACY_POLICY = 'privacy_policy'; // 隐私政策
    public const ABOUT_US = 'about_us'; // 关于我们

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }
}
