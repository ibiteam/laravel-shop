<?php

namespace App\Messages;

use App\Models\PhoneMsg;
use Illuminate\Support\Carbon;
use Overtrue\EasySms\Contracts\GatewayInterface;

class PhoneCodeMessage extends BaseMessage
{
    public function __construct($info, $type = PhoneMsg::PHONE_LOGIN)
    {
        $this->setSendType($type);
        //存入数据库的数据
        $code = mt_rand(100000, 999999);

        $this->setInfo([
            'info' => $info,
            'code' => $code,
            'type' => $type,  //发送验证码的类型
            'end_time' => Carbon::now()->addMinutes(PhoneMsg::CODE_TIME)->getTimestamp(),
        ]);

        parent::__construct(['data' => ['code' => $code, 'time' => PhoneMsg::CODE_TIME]]);
    }

    public function getTemplate(?GatewayInterface $gateway = null)
    {
        return config('custom.sms_templates.phone_code');
    }
}
