<?php

namespace App\Enums;

enum CustomCodeEnum: int
{
    case SUCCESS = 200; // 访问成功
    case ERROR = 400; // 访问失败
    case UNAUTHORIZED = 401; // 未登录
    case FORBIDDEN = 403; // 无权限

    case GOODS_DESTROY = 4000; // 商品被删除
    case GOODS_OFF_SALE = 4001; // 商品被下架
    case GOODS_SOLD_OUT = 4002; // 商品已售完
}
