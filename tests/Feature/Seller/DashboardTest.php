<?php

it('home', function () {
    $response = $this->doGet('api/seller/home');
    dump($response);
});
