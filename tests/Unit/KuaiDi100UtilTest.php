<?php


use App\Services\ExpressService;

it('test get query express', function () {
    $res = (new ExpressService)->queryExpress('2222222222222', 'jd');

});
