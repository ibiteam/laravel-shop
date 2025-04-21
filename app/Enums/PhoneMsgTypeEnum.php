<?php

namespace App\Enums;

use App\Exceptions\BusinessException;
use App\Services\SmsService;

enum PhoneMsgTypeEnum: int
{

    public function getLabel(): string
    {
        return match ($this) {
            self::PHONE_LOGIN => '登录短信验证码',
            self::PHONE_FORGET_PASSWORD => '忘记密码短信验证码',
            self::PHONE_NOTICE => '通知类短信',
            self::PHONE_EDIT_PASSWORD => '修改密码短信验证码',
            self::PHONE_EDIT => '修改手机号验证码',
            self::ACTION_VERIFY_PHONE => '验证手机号验证码',
            default => ''
        };
    }

    public static function getEnumValue($action): self
    {
        return match ($action) {
            SmsService::ACTION_LOGIN => self::PHONE_LOGIN,
            SmsService::ACTION_FORGET_PASSWORD => self::PHONE_FORGET_PASSWORD,
            SmsService::ACTION_EDIT_PASSWORD => self::PHONE_EDIT_PASSWORD,
            SmsService::ACTION_EDIT_PHONE => self::PHONE_EDIT,
            SmsService::ACTION_VERIFY_PHONE => self::ACTION_VERIFY_PHONE,
            default => throw new BusinessException('Unexpected match value'),
        };
    }

    /**
     * @throws BusinessException
     */
    public static function getLabelBySource(int $source): string
    {
        return self::formSource($source)->getLabel();
    }

    /**
     * @throws BusinessException
     */
    public static function formSource(?int $source): self
    {
        return match ($source) {
            2 => self::PHONE_LOGIN,
            3 => self::PHONE_FORGET_PASSWORD,
            4 => self::PHONE_NOTICE,
            5 => self::PHONE_EDIT_PASSWORD,
            6 => self::PHONE_EDIT,
            7 => self::ACTION_VERIFY_PHONE,
            default => throw new BusinessException('Unexpected match value'),
        };
    }



    case PHONE_LOGIN = 2;

    case PHONE_FORGET_PASSWORD = 3;

    case PHONE_NOTICE = 4;

    case PHONE_EDIT_PASSWORD = 5;
    case PHONE_EDIT = 6;
    case ACTION_VERIFY_PHONE = 7;
}
