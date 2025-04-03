<?php

namespace App\Enums;

use App\Exceptions\BusinessException;

enum PhoneMsgTypeEnum: int
{

    public function getLabel(): string
    {
        return match ($this) {
            self::PHONE_LOGIN => '登录短信验证码',
            self::PHONE_FORGET_PASSWORD => '忘记密码短信验证码',
            self::PHONE_NOTICE => '通知类短信',
            self::PHONE_EDIT_PASSWORD => '修改密码短信验证码',
            default => ''
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
            default => throw new BusinessException('Unexpected match value'),
        };
    }

    case PHONE_LOGIN = 2;

    case PHONE_FORGET_PASSWORD = 3;

    case PHONE_NOTICE = 4;

    case PHONE_EDIT_PASSWORD = 5;
}
