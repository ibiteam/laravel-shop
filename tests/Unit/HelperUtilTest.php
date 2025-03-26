<?php

it('test get sensitive words', function () {
    $res = get_sensitive_words('测试敏感词');
    dd($res);
});
