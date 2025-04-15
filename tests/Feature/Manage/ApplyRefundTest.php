<?php

it('detail', function () {
    $response = $this->doGet('api/manage/order/apply_refund/detail', [
        'id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

