<?php

namespace App\Utils\Wechat;

use App\Enums\PayFormEnum;
use EasyWeChat\Kernel\Contracts\Server as WechatPayServer;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Server;
use Illuminate\Support\Facades\Log;

class WechatPayUtil
{
    protected ?Application $application = null;

    /**
     * @throws \Exception
     */
    public function __construct(?array $config, PayFormEnum $pay_form_enum)
    {
        $app_id = match ($pay_form_enum) {
            PayFormEnum::PAY_FORM_MINI => config('easywechat.mini_app.default.app_id'),
            PayFormEnum::PAY_FORM_APP => $config['app_wechat_pay_app_id'],
            default => config('easywechat.official_account.default.app_id')
        };

        $this->application = new Application([
            'app_id' => $app_id,
            'mch_id' => $config['mch_id'],
            'notify_url' => route('notify.wechat.pay'),
            'private_key' => $config['private_key'],
            'certificate' => $config['certificate'],
            'v2_secret_key' => $config['v2_secret_key'],
            'secret_key' => $config['secret_key'],
            'http' => [
                'throw' => false,
            ],
        ]);
    }

    /**
     * 获取应用实例.
     */
    public function application(): ?Application
    {
        return $this->application;
    }

    /**
     * 服务器端回调.
     *
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function server(): Server|WechatPayServer
    {
        return $this->application->getServer();
    }

    /**
     * 查询订单支付信息.
     *
     *
     * @throws \Exception
     */
    public function queryOrder(string $out_trade_no): array
    {
        try {
            return $this->application->getClient()->get("/v3/pay/transactions/out-trade-no/$out_trade_no", [
                'mchid' => $this->application->getMerchant()->getMerchantId(),
            ])->toArray();
        } catch (\Throwable $throwable) {
            Log::error('微信支付查询订单失败：'.$throwable->getMessage(), $throwable->getTrace());

            throw new \Exception('微信支付查询订单失败');
        }
    }

    /**
     * V3 H5网页版支付.
     *
     * @throws \Exception
     */
    public function h5Pay(string $description, string $out_trade_no, float|int $amount, string $redirect_url): string
    {
        try {
            $response = $this->application->getClient()->postJson('/v3/pay/transactions/h5', array_merge([
                'description' => $description,
                'out_trade_no' => $out_trade_no,
                'amount' => [
                    'total' => (int) ($amount * 100),
                    'currency' => 'CNY',
                ],
                'scene_info' => [
                    'payer_client_ip' => get_request_ip(),
                    'h5_info' => ['type' => 'Wap'],
                ],
            ], $this->getDefaultParams()));

            $content = $response->toArray();

            if (! isset($content['h5_url'])) {
                throw new \Exception('Wechat H5 Pay Error:'.json_encode($content));
            }

            return $content['h5_url'].'&redirect_url='.urlencode($redirect_url);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());

            throw new \Exception('微信支付失败');
        }
    }

    /**
     * V3 JS支付.
     *
     * @throws \Exception
     */
    public function jsPay(string $openid, string $description, string $out_trade_no, float|int $amount): array
    {
        try {
            $params = array_merge([
                'description' => $description,
                'out_trade_no' => $out_trade_no,
                'amount' => [
                    'total' => (int) ($amount * 100),
                    'currency' => 'CNY',
                ],
                'payer' => [
                    'openid' => $openid,
                ],
                'scene_info' => [
                    'payer_client_ip' => get_request_ip(),
                ],
            ], $this->getDefaultParams());
            $response = $this->application->getClient()->postJson('/v3/pay/transactions/jsapi', $params);

            $content = $response->toArray();

            if (! isset($content['prepay_id'])) {
                throw new \Exception('Wechat Js Pay response Error'.json_encode($content));
            }

            return $this->application->getUtils()->buildBridgeConfig($content['prepay_id'], $params['appid']);
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());

            throw new \Exception('微信支付失败');
        }
    }

    /**
     * V3 APP支付.
     *
     * @throws \Exception
     */
    public function appPay(string $description, string $out_trade_no, float|int $amount): array
    {
        try {
            $params = array_merge([
                'description' => $description,
                'out_trade_no' => $out_trade_no,
                'amount' => [
                    'total' => (int) ($amount * 100),
                    'currency' => 'CNY',
                ],
                'scene_info' => [
                    'payer_client_ip' => get_request_ip(),
                ],
            ], $this->getDefaultParams());
            $response = $this->application->getClient()->postJson('/v3/pay/transactions/app', $params);

            $content = $response->toArray();

            if (! isset($content['prepay_id'])) {
                throw new \Exception('Wechat app Pay response Error'.json_encode($content));
            }
            $data = [
                'appid' => $params['appid'],
                'partnerid' => $params['mchid'],
                'prepayid' => $content['prepay_id'],
                'noncestr' => uniqid(),
                'timestamp' => time(),
                'package' => 'Sign=WXPay',
            ];

            $data['sign'] = $this->generateSign($data, $this->application->getMerchant()->getV2SecretKey());

            return $data;
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());

            throw new \Exception('微信支付失败');
        }
    }

    /**
     * 生成签名.
     */
    private function generateSign(array $attributes, string $key): string
    {
        ksort($attributes);

        $attributes['key'] = $key;

        return strtoupper(call_user_func_array('md5', [urldecode(http_build_query($attributes))]));
    }

    /**
     * 生成默认请求参数.
     */
    private function getDefaultParams(): array
    {
        return [
            'appid' => $this->application->getConfig()->get('app_id'),
            'mchid' => $this->application->getConfig()->get('mch_id'),
            'notify_url' => $this->application->getConfig()->get('notify_url'),
        ];
    }
}
