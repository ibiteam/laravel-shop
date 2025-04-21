<?php

namespace App\Http\Dao;

use App\Enums\PhoneMsgTypeEnum;
use App\Models\PhoneMsg;
use Illuminate\Support\Carbon;

class PhoneMsgDao
{
    /**
     * 根据手机号获取信息.
     *
     * @param int              $phone  手机号
     * @param PhoneMsgTypeEnum $enum   发送类型
     * @param int              $status 状态
     */
    public function getInfoByPhoneAndType(int $phone, PhoneMsgTypeEnum $enum, int $status = PhoneMsg::STATUS_NOT_USED): ?PhoneMsg
    {
        return PhoneMsg::query()->wherePhone($phone)->whereType($enum->value)->whereStatus($status)->orderBy('id', 'desc')->first();
    }

    /**
     * 根据手机号和验证码获取信息.
     *
     * @param int              $phone 手机号
     * @param string           $code  短信验证码
     * @param PhoneMsgTypeEnum $enum  发送类型
     */
    public function getInfoByCheckCode(int $phone, string $code, PhoneMsgTypeEnum $enum): ?PhoneMsg
    {
        return PhoneMsg::query()->wherePhone($phone)->whereCode($code)->whereStatus(PhoneMsg::STATUS_NOT_USED)->whereType($enum->value)->orderBy('id', 'desc')->first();
    }

    /**
     * 获取当天发送数量.
     *
     * @param int              $phone  手机号
     * @param PhoneMsgTypeEnum $enum   发送类型
     * @param int              $status 状态
     */
    public function getCountByDay(int $phone, PhoneMsgTypeEnum $enum, int $status = PhoneMsg::STATUS_NOT_USED): int
    {
        return PhoneMsg::query()
            ->wherePhone($phone)
            ->whereType($enum->value)
            ->whereStatus($status)
            ->whereBetween('start_time', [Carbon::now()->startOfDay()->getTimestamp(), Carbon::now()->endOfDay()->getTimestamp()])
            ->count();
    }
}
