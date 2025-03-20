<?php

namespace App\Services;

use App\Enums\CustomCodeEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\PhoneMsgDao;
use App\Http\Dao\UserDao;
use App\Messages\BaseMessage;
use App\Messages\PhoneCodeMessage;
use App\Models\PhoneMsg;
use App\Models\User;
use App\Utils\SmsUtil;

class SmsService
{
    public const ACTION_LOGIN = 'login';
    public const ACTION_REGISTER = 'register';

    public function sendOtp(string $action, int $phone): bool
    {
        return match ($action) {
            self::ACTION_LOGIN => $this->sendLoginOtp($phone),
            self::ACTION_REGISTER => $this->sendRegisterOtp($phone),
            default => throw new BusinessException('发送失败~'),
        };
    }

    public function sendLoginOtp(int $phone): bool
    {
        $user = app(UserDao::class)->getInfoByPhone($phone);

        if (! $user instanceof User) {
            throw new BusinessException('该手机号未注册');
        }

        $this->sendMessage($phone, new PhoneCodeMessage('登录短信', PhoneMsg::PHONE_LOGIN));

        return true;
    }

    public function sendRegisterOtp(int $phone): bool
    {
        $user = app(UserDao::class)->getInfoByPhone($phone);

        if ($user instanceof User) {
            throw new BusinessException('该手机号已被注册');
        }

        $this->sendMessage($phone, new PhoneCodeMessage('注册短信', PhoneMsg::PHONE_REGISTER));

        return true;
    }

    /**
     * 验证短信验证码
     *
     * @param int    $phone     手机号
     * @param string $otp       短信验证码
     * @param int    $send_type 短信类型
     *
     * @throws BusinessException
     */
    public function verifyOtp(int $phone, string $otp, int $send_type = PhoneMsg::PHONE_NOTICE): bool
    {
        $message = app(PhoneMsgDao::class)->getInfoByCheckCode($phone, $otp, $send_type);

        if (! $message instanceof PhoneMsg) {
            throw new BusinessException('短信验证码输入错误', CustomCodeEnum::OTP_INVALID);
        }
        $message->status = PhoneMsg::STATUS_USED;
        $message->save();

        if ($message->end_time < time()) {
            throw new BusinessException('验证码已经失效，请重新获取', CustomCodeEnum::OTP_EXPIRED);
        }

        return true;
    }

    private function sendMessage(int $phone, BaseMessage $message): void
    {
        // 检查是否在 60 秒内已发送未验证的验证码
        $phone_msg = app(PhoneMsgDao::class)->getInfoByPhoneAndType($phone, $message->getSendType());

        if ($phone_msg instanceof PhoneMsg) {
            $tmp_timestamp = time();

            if ($phone_msg->start_time < $tmp_timestamp && $phone_msg->start_time + 60 > $tmp_timestamp) {
                throw new BusinessException('请在 60 秒后再尝试发送验证码。', CustomCodeEnum::OTP_TOO_FREQUENT);
            }
        }

        if (app(PhoneMsgDao::class)->getCountByDay($phone, $message->getSendType()) >= 5) {
            throw new BusinessException('您今日发送短信验证码次数已超过限制。', CustomCodeEnum::OTP_TOO_FREQUENT);
        }

        try {
            app(SmsUtil::class)->send($phone, $message);
        } catch (BusinessException $business_exception) {
            throw new BusinessException($business_exception->getMessage(), CustomCodeEnum::OTP_SENT_FAILED);
        } catch (\Throwable $throwable) {
            throw new BusinessException('短信发送失败！', CustomCodeEnum::OTP_SENT_FAILED);
        }
    }
}
