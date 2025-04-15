<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ManageTestCase extends BaseTestCase
{
    public string $account = 'admin';

    public string $password = 'Aa123456';

    public string $source = 'pc';

    public function doPost(string $url, array $data = []): mixed
    {
        return $this->postJson(url($url), $data, [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->getAccountToken(),
            'source' => $this->source,
        ])->json();
    }

    public function doGet(string $url, array $data = []): mixed
    {
        return $this->getJson(url($url).'?'.http_build_query($data), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->getAccountToken(),
            'source' => $this->source,
        ])->json();
    }

    public function getAccountToken()
    {
        $token = '';
        $access_token_file = $this->access_token_file();

        if (! file_exists($access_token_file)) {
            $data = [
                'user_name' => $this->account,
                'password' => $this->password,
            ];
            $url = 'api/manage/login';

            $res = $this->json('post', url($url), $data, [
                'X-Requested-With' => 'XMLHttpRequest',
            ])->json();

            if ($res && isset($res['code']) && (int) $res['code'] === Response::HTTP_OK) {
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
        return storage_path('logs/manage_access_token');
    }
}
