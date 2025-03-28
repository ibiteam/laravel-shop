<?php

namespace App\Enums;

enum ResponseEnum: int
{
    case SUCCESS = 200; // 访问成功
    case ERROR = 400; // 访问失败
    case UNAUTHORIZED = 401; // 未登录
    case FORBIDDEN = 403; // 无权限
}
