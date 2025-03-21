<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public string $token = '';

    public string $source = 'h5';

    public function doPost(string $url,array $data = []): mixed
    {
        return $this->postJson(url($url),$data,[
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->token,
            'source' => $this->source,
        ])->json();
    }

    public function doGet(string $url, array $data = []): mixed
    {
        return $this->getJson(url($url) . '?' . http_build_query($data), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Authorization' => 'Bearer '.$this->token,
            'source' => $this->source,
        ])->json();
    }
}
