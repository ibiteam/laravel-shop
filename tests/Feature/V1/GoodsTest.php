<?php

it('test goods detail api interface', function () {
    $response = $this->doGet('api/v1/goods', ['no' => 'eda482c3-df45-47a4-abc5-f795db6fefae']);
    dd($response);
});
