<?php

it('detail', function () {
    $response = $this->doGet('api/v1/article/detail', ['article_id' => 1]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($res);
});
