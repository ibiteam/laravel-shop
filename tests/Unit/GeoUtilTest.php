<?php

use App\Utils\AppService\GeoUtil;

it('test getAddressByIp', function () {
    $res = (new GeoUtil(0))->getAddressByIp('27.196.91.0');
    dump(json_encode($res));
    // {"status":"1","info":"OK","infocode":"10000","province":"\u5c71\u4e1c\u7701","city":"\u804a\u57ce\u5e02","adcode":"371500","rectangle":"115.7709432,36.31274774;116.2016523,36.57071727"}
});


it('test getRegeoByLocation', function () {
    $res = (new GeoUtil(0))->getRegeoByLocation('115.7709432,36.31274774');
    dump(json_encode($res));
    // {"status":"1","regeocode":{"roads":[],"roadinters":[],"formatted_address":"\u5c71\u4e1c\u7701\u804a\u57ce\u5e02\u4e1c\u660c\u5e9c\u533a\u6c99\u9547\u9547","addressComponent":{"city":"\u804a\u57ce\u5e02","province":"\u5c71\u4e1c\u7701","adcode":"371502","district":"\u4e1c\u660c\u5e9c\u533a","towncode":"371502102000","streetNumber":{"number":[],"direction":[],"distance":[],"street":[]},"country":"\u4e2d\u56fd","township":"\u6c99\u9547\u9547","businessAreas":[[]],"building":{"name":[],"type":[]},"neighborhood":{"name":[],"type":[]},"citycode":"0635"},"aois":[],"pois":[]},"info":"OK","infocode":"10000"}
});

it('test getGeocodingByAddress', function () {
    $res = (new GeoUtil(0))->getGeocodingByAddress('北京市丰台区国联经济中心');
    dump(json_encode($res));
    // {"status":"1","info":"OK","infocode":"10000","count":"1","geocodes":[{"formatted_address":"北京市丰台区","country":"中国","province":"北京市","citycode":"010","city":"北京市","district":"丰台区","township":[],"neighborhood":{"name":[],"type":[]},"building":{"name":[],"type":[]},"adcode":"110106","street":[],"number":[],"location":"116.286726,39.858538","level":"区县"}]}
});
