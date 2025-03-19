<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $parent_id  父级ID
 * @property string                          $code       标识
 * @property string|null                     $value      值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopConfig whereValue($value)
 *
 * @mixin \Eloquent
 */
class ShopConfig extends Model
{
    use DatetimeTrait;
    public const BASE_SETTINGS = 'base_settings'; // 基础设置
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

    public const MANAGE_SETTINGS = 'manage_settings'; // 后台设置
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
