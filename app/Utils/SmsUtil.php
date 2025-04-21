<?php

namespace App\Utils;

use App\Exceptions\BusinessException;
use App\Http\Dao\ShopConfigDao;
use App\Messages\BaseMessage;
use App\Models\PhoneMsg;
use App\Models\ShopConfig;
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

    private function toDatabase(int $phone, BaseMessage $message): void
    {
        $now_timestamp = Carbon::now()->getTimestamp();
        $info = [
            'phone' => $phone,
            'code' => '',
            'type' => $message->getEnum()->value,
            'start_time' => $now_timestamp,
            'end_time' => $now_timestamp,
            'ip' => get_request_ip(),
            'info' => '',
            'status' => PhoneMsg::STATUS_NOT_USED,
        ];

        // 合并消息附加信息，确保 $message->info 是数组
        if ($tmp_info = $message->getInfo()) {
            $info = array_merge($info, $tmp_info);
        }

        PhoneMsg::query()->create($info);
    }

    private function config(): array
    {
        $shop_configs = app(ShopConfigDao::class)->multipleConfig(
            ShopConfig::SMS_DRIVER,
            ShopConfig::SMS_ACCESS_KEY,
            ShopConfig::SMS_ACCESS_SECRET,
            ShopConfig::SMS_SIGN_NAME
        );

        return [
            'timeout' => 10.0,
            'default' => [
                'strategy' => OrderStrategy::class,
                'gateways' => [$shop_configs[ShopConfig::SMS_DRIVER]],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => storage_path('logs/message.log'),
                ],
                'aliyun' => [
                    'access_key_id' => $shop_configs[ShopConfig::SMS_ACCESS_KEY],
                    'access_key_secret' => $shop_configs[ShopConfig::SMS_ACCESS_SECRET],
                    'sign_name' => $shop_configs[ShopConfig::SMS_SIGN_NAME],
                ],
            ],
        ];
    }
}
