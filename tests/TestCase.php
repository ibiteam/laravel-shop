<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\JsonResponse;

abstract class TestCase extends BaseTestCase
{
    public string $account = 'laravel_shop';

    public string $password = 'laravel-shop-1';

    public int $phone = 13100000000;

    public string $code = '000000';

    public string $login_type = 'phone';  // 登录类型 password / phone

    public string $source = 'h5';

    public function doPost(string $url, array $data = []): mixed
    {
        return $this->postJson(url($url), $data, [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->getAccountToken($this->login_type),
            'source' => $this->source,
        ])->json();
    }

    public function doGet(string $url, array $data = []): mixed
    {
        return $this->getJson(url($url).'?'.http_build_query($data), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->getAccountToken($this->login_type),
            'source' => $this->source,
        ])->json();
    }

    public function getAccountToken($login_type)
    {
        $token = '';
        $access_token_file = $this->access_token_file();

        if (! file_exists($access_token_file)) {
            if ($login_type === 'phone') {
                $data = [
                    'phone' => $this->phone,
                    'code' => $this->code,
                ];

                $url = 'api/v1/auth/login/phone';
            } elseif ($login_type === 'password') {
                $data = [
                    'account' => $this->account,
                    'password' => md5($this->password),
                ];
                $url = 'api/v1/auth/login/password';
            } else {
                return '';
            }

            $res = $this->json('post', url($url), $data, [
                'X-Requested-With' => 'XMLHttpRequest',
            ])->json();

            if ($res && isset($res['code']) && (int) $res['code'] === JsonResponse::HTTP_OK) {
                $token = $res['data']['token'];
                file_put_contents($access_token_file, $token);
            }
        } else {
            $token = file_get_contents($access_token_file);
        }

        return $token;
    }

    private function access_token_file(): string
    {
        return storage_path('logs/access_token');
    }
}
