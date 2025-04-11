<?php

namespace App\Utils\Wechat;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;
use Overtrue\LaravelWeChat\EasyWeChat;
use Overtrue\Socialite\Contracts\UserInterface;

class WechatOfficialAccountUtil
{
    protected ?Application $application = null;

    public function __construct()
    {
        $this->application = EasyWeChat::officialAccount();
    }

    /**
     * 获取应用实例.
     */
    public function application(): ?Application
    {
        return $this->application;
    }

    /**
     * 通过code获取用户信息.
     */
    public function getUserFromCode(string $code): ?UserInterface
    {
        try {
            return $this->application->getOAuth()->userFromCode($code);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}
