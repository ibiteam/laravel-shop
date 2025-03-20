<?php

namespace App\Utils;

use App\Exceptions\BusinessException;
use App\Messages\BaseMessage;
use App\Models\PhoneMsg;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Strategies\OrderStrategy;

class SmsUtil
{
    protected EasySms $easySms;

    public function __construct()
    {
        $this->easySms = new EasySms($this->config());
    }

    /**
     * 发送短信
     *
     * @throws BusinessException
     */
    public function send($phone, BaseMessage $message): bool
    {
        // 发送短信
        try {
            $this->toDatabase($phone, $message);

            if (is_pro_env()) {
                $this->easySms->send($phone, $message);
            }

            return true;
        } catch (\Throwable $exception) {
            // 处理发送失败的情况
            Log::error("发送短信失败，手机号：{$phone}: ".$exception->getMessage(), $exception->getTrace());

            throw new BusinessException('短信发送失败，请稍后再试。');
        }
    }

    private function toDatabase(int $phone, BaseMessage $message): PhoneMsg
    {
        $now_timestamp = Carbon::now()->getTimestamp();
        $info = [
            'phone' => $phone,
            'code' => '',
            'type' => '',
            'start_time' => $now_timestamp,
            'end_time' => $now_timestamp,
            'ip' => get_request_ip(),
            'info' => PhoneMsg::PHONE_NOTICE,
            'status' => PhoneMsg::STATUS_NOT_USED,
        ];

        // 合并消息附加信息，确保 $message->info 是数组
        if ($tmp_info = $message->getInfo()) {
            $info = array_merge($info, $tmp_info);
        }

        return PhoneMsg::query()->create($info);
    }

    private function config(): array
    {
        return [
            'timeout' => 10.0,
            'default' => [
                'strategy' => OrderStrategy::class,
                'gateways' => ['aliyun'],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => storage_path('logs/message.log'),
                ],
                'aliyun' => [
                    'access_key_id' => config('custom.sms_access_key'),
                    'access_key_secret' => config('custom.sms_access_secret'),
                    'sign_name' => config('custom.sms_sign_name'),
                ],
            ],
        ];
    }
}
