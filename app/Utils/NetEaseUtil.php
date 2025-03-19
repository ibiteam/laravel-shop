<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NetEaseUtil
{
    private function __construct() {}

    /**
     * 校验验证码
     */
    public static function verifyCaptcha(string $validate): bool
    {
        // 组合参数
        $params = [
            'captchaId' => config('custom.net_east_yi_dun.slider_captcha'),
            'validate' => $validate,
            'user' => "{'user':123456}",
            'secretId' => config('custom.net_east_yi_dun.secret_id'),
            'version' => config('custom.net_east_yi_dun.version'),
            'timestamp' => sprintf('%d', round(microtime(true) * 1000)),
            'nonce' => sprintf('%d', rand()),
        ];

        $res = self::captchaDoPost($params);

        if (! $res || ! isset($res['result']) || ! $res['result']) {
            return false;
        }

        return true;
    }

    private static function captchaDoPost(array $params): array
    {
        $client = new Client([
            'timeout' => 10,
            'connect_timeout' => 10,
        ]);

        try {
            $response = $client->post(config('custom.net_east_yi_dun.host'), [
                'form_params' => self::sign($params),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $throwable) {
            Log::error('网易易盾请求失败：'.$throwable->getMessage(), $throwable->getTrace());

            return [];
        }
    }

    private static function sign(array $params): array
    {
        ksort($params);
        $buff = '';

        foreach ($params as $key => $value) {
            $buff .= $key.$value;
        }
        $buff .= config('custom.net_east_yi_dun.secret_key');

        $params['signature'] = md5(mb_convert_encoding($buff, 'utf8', 'auto'));

        return $params;
    }
}
