<?php

namespace App\Enums;

enum CustomCodeEnum: int
{
    case SUCCESS = 200; // 访问成功
    case ERROR = 400; // 访问失败
    case UNAUTHORIZED = 401; // 未登录
    case FORBIDDEN = 403; // 无权限

    case OTP_EXPIRED = 1001;        // 验证码已过期
    case OTP_INVALID = 1002;        // 验证码错误
    case OTP_TOO_FREQUENT = 1003;   // 短信发送太频繁，60秒内重复发送
    case OTP_SENT_SUCCESSFULLY = 1004; // 短信验证码发送成功
    case OTP_SENT_FAILED = 1005; // 短信验证码发送失败
    case OTP_SENT_NOT_REGISTER = 1006; // 短信验证码登录验证手机号是否注册
}
