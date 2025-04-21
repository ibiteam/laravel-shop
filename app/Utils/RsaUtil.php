<?php

namespace App\Utils;

use App\Models\ShopConfig;
use Illuminate\Support\Facades\Log;

class RsaUtil
{
    private function __construct() {}

    public static function getDecodeData($data, $referer = 'manage'): false|string
    {
        $private_key = match ($referer) {
            'manage' => shop_config(ShopConfig::MANAGE_LOGIN_RSA_PRIVATE_KEY, ''),
            default => false,
        };

        if (! $private_key) {
            Log::error('接口安全解密失败,没有查询到对应端口的RSA密钥');

            return false;
        }

        $res = openssl_private_decrypt(base64_decode($data), $result, openssl_get_privatekey($private_key));

        return $res ? $result : false;
    }
}
