<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

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
class ShopConfig extends Model
{
    use DatetimeTrait;
    public const GROUP_BASE_SETTINGS = 'base_settings'; // 基础设置组名
    public const SHOP_NAME = 'shop_name'; // 商城名称
    public const SHOP_KEYWORDS = 'shop_keywords'; // 商城关键词
    public const SHOP_DESCRIPTION = 'shop_description'; // 商城描述
    public const SHOP_ICON = 'shop_icon'; // 商城 ICON
    public const SHOP_LOGO = 'shop_logo'; // 商城 LOGO
    public const SHOP_MANAGE_LOGIN_IMAGE = 'shop_manage_login_image'; // 商城后台登录页面图片
    public const BANK_ACCOUNT = 'bank_account'; // 银行账号
    public const SHOP_ADDRESS = 'shop_address'; // 商城线下地址
    public const SERVICE_MOBILE = 'service_mobile'; // 服务热线
    public const ICP_NUMBER = 'icp_number'; // ICP 备案号
    public const SHOP_COLOR = 'shop_color'; // 主题色
    public const IS_GRAY = 'is_gray'; // 网站首页是否置灰

    public const CURRENCY_FORMAT = 'currency_format'; // 对价格进行格式化
    public const PRICE_FORMAT = 'price_format'; // 保留几位小数
    public const SMTP_HOST = 'smtp_host'; // 发送邮件服务器地址(SMTP)
    public const SMTP_PORT = 'smtp_port'; // 服务器端口
    public const SMTP_USER = 'smtp_user'; // 邮件发送账号
    public const SMTP_PASS = 'smtp_pass'; // 账号密码

    public const GROUP_MANAGE_SETTINGS = 'manage_settings'; // 后台设置组名
    public const MANAGE_LOGIN_RSA_PUBLIC_KEY = 'manage_login_rsa_public_key'; // 后台登录RSA公钥
    public const MANAGE_LOGIN_RSA_PRIVATE_KEY = 'manage_login_rsa_private_key'; // 后台登录RSA私钥

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }
}
