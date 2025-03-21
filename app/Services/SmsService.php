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
    public const ACTION_FORGET_PASSWORD = 'password-forget';
    public const ACTION_EDIT_PASSWORD = 'password-edit'; // 修改密码

    /**
     * 发送短信验证码
     *
     * @throws BusinessException
     */
    public function sendOtp(string $action, int $phone, ?User $user = null): bool
    {
        return match ($action) {
            self::ACTION_LOGIN => $this->sendLoginOtp($phone),
            self::ACTION_REGISTER => $this->sendRegisterOtp($phone),
            self::ACTION_FORGET_PASSWORD => $this->sendForgetPasswordOtp($phone),
            self::ACTION_EDIT_PASSWORD => $this->sendEditPasswordOtp($user),
            default => throw new BusinessException('发送失败~'),
        };
    }

    /**
     * @throws BusinessException
     */
    public function sendEditPasswordOtp(?User $user): bool
    {
        if (! $user instanceof User) {
            throw new BusinessException('用户未登录', CustomCodeEnum::UNAUTHORIZED);
        }

        $this->sendMessage($user->phone, new PhoneCodeMessage('修改密码短信', PhoneMsg::PHONE_EDIT_PASSWORD));

        return true;
    }

    /**
     * 登录短信验证码
     *
     * @throws BusinessException
     */
    public function sendLoginOtp(int $phone): bool
    {
        $user = app(UserDao::class)->getInfoByPhone($phone);

        if (! $user instanceof User) {
            throw new BusinessException('该手机号未注册');
        }

        $this->sendMessage($phone, new PhoneCodeMessage('登录短信', PhoneMsg::PHONE_LOGIN));

        return true;
    }

    /**
     * 忘记密码短信验证码
     *
     * @throws BusinessException
     */
    public function sendForgetPasswordOtp(int $phone): bool
    {
        $user = app(UserDao::class)->getInfoByPhone($phone);

        if (! $user instanceof User) {
            throw new BusinessException('该手机号未注册');
        }

        $this->sendMessage($phone, new PhoneCodeMessage('忘记密码短信', PhoneMsg::PHONE_FORGET_PASSWORD));

        return true;
    }

    /**
     * 注册短信验证码
     *
     * @throws BusinessException
     */
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
        if (! is_pro_env() && $otp === '000000') {
            return true;
        }

        $message = app(PhoneMsgDao::class)->getInfoByCheckCode($phone, $otp, $send_type);

        if (! $message instanceof PhoneMsg) {
            throw new BusinessException('短信验证码输入错误');
        }
        $message->status = PhoneMsg::STATUS_USED;
        $message->save();

        if ($message->end_time < time()) {
            throw new BusinessException('验证码已经失效，请重新获取');
        }

        return true;
    }

    /**
     * 发送短信验证码
     *
     * @throws BusinessException
     */
    private function sendMessage(int $phone, BaseMessage $message): void
    {
        // 检查是否在 60 秒内已发送未验证的验证码
        $phone_msg = app(PhoneMsgDao::class)->getInfoByPhoneAndType($phone, $message->getSendType());

        if ($phone_msg instanceof PhoneMsg) {
            $tmp_timestamp = time();

            if ($phone_msg->start_time < $tmp_timestamp && $phone_msg->start_time + 60 > $tmp_timestamp) {
                throw new BusinessException('请在 60 秒后再尝试发送验证码。');
            }
        }

        if (app(PhoneMsgDao::class)->getCountByDay($phone, $message->getSendType()) >= 5) {
            throw new BusinessException('您今日发送短信验证码次数已超过限制。');
        }

        try {
            app(SmsUtil::class)->send($phone, $message);
        } catch (BusinessException $business_exception) {
            throw new BusinessException($business_exception->getMessage());
        } catch (\Throwable $throwable) {
            throw new BusinessException('短信发送失败！');
        }
    }
}
