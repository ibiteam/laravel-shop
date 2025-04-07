<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\JsonResponse;

abstract class TestCase extends BaseTestCase
{
    public string $account = 'laravel_shop';

    public string $password = 'laravel-shop-1';

    public string $source = 'h5';

    public function doPost(string $url,array $data = []): mixed
    {
        return $this->postJson(url($url),$data,[
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '. $this->getAccountToken(),
            'source' => $this->source,
        ])->json();
    }

    public function doGet(string $url, array $data = []): mixed
    {
        return $this->getJson(url($url) . '?' . http_build_query($data), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '. $this->getAccountToken(),
            'source' => $this->source,
        ])->json();
    }


    public function getAccountToken()
    {
        $token = '';
        $access_token_file = $this->access_token_file();
        if (! file_exists($access_token_file)) {
            $data = [
                'account' => $this->account,
                'password' => $this->password,
            ];
            $res = $this->json('post', url('api/v1/auth/login/password'), $data, [
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

    private function access_token_file()
    {
        return storage_path('logs/access_token');
    }
}
