<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NetEaseUtil
{
    /**
     * 校验验证码
     */
    public function verifyCaptcha(string $validate): bool
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

        $res = $this->captchaDoPost($params);

        if (! $res || ! isset($res['result']) || ! $res['result']) {
            return false;
        }

        return true;
    }

    private function captchaDoPost(array $params): array
    {
        $client = new Client([
            'timeout' => 10,
            'connect_timeout' => 10,
        ]);

        $params['signature'] = $this->sign($params);

        try {
            $response = $client->post(config('custom.net_east_yi_dun.host'), [
                'form_params' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $throwable) {
            Log::error('网易易盾请求失败：'.$throwable->getMessage(), $throwable->getTrace());

            return [];
        }
    }

    private function sign(array $params): string
    {
        ksort($params);
        $buff = '';

        foreach ($params as $key => $value) {
            $buff .= $key.$value;
        }
        $buff .= config('custom.net_east_yi_dun.secret_key');

        return md5(mb_convert_encoding($buff, 'utf8', 'auto'));
    }
}
