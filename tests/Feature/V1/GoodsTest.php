<?php

it('test goods detail api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $response = $this->doGet("api/v1/goods/{$goods_sn}");
    dd($response);
});
it('test goods sku detail api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $goods_sku_unique = '13_14_15';
    $response = $this->doGet("api/v1/goods/{$goods_sn}/{$goods_sku_unique}");
    dd($response);
});
