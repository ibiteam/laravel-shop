<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Http\Dao\TransactionDao;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShopConfig;
use App\Utils\Wechat\WechatPayUtil;

class WechatPayOrderService implements PayOrderInterface
{
    /**
     * @throws \Exception
     */
    public function orderPay(Order $order, Payment $payment, PayFormEnum $pay_form_enum): array
    {
        $config = $payment->config;

        $init_config = [
            'app_id' => '',
            'mch_id' => $config['mch_id'],
            'notify_url' => route('notify.wechat.pay'),
            'private_key' => $config['private_key'],
            'certificate' => $config['certificate'],
            'v2_secret_key' => $config['v2_secret_key'],
            'secret_key' => $config['secret_key'],
        ];

        // 创建支付流水记录
        $transaction = app(TransactionDao::class)->storeByOrder($order, $payment, $pay_form_enum->getLabel());

        switch ($pay_form_enum) {
            case PayFormEnum::PAY_FORM_APP:
                $init_config['app_id'] = $config['app_wechat_pay_app_id'];
                $payment = new WechatPayUtil($init_config);

                $pay_info = $payment->appPay(
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount
                );

                break;

            case PayFormEnum::PAY_FORM_MINI:
                // todo 如何获取 openid
                $openid = '';
                $init_config['app_id'] = $config['mini_wechat_pay_app_id'];
                $payment = new WechatPayUtil($init_config);
                $pay_info = $payment->jsPay(
                    $openid,
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount
                );

                break;

            case PayFormEnum::PAY_FORM_WECHAT:
                // todo 如何获取 openid
                $openid = '';
                $init_config['app_id'] = $config['service_wechat_pay_app_id'];
                $payment = new WechatPayUtil($init_config);

                $pay_info = $payment->jsPay(
                    $openid,
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount
                );

                break;

            case PayFormEnum::PAY_FORM_H5:
                // todo H5支付成功的地址
                $init_config['app_id'] = $config['service_wechat_pay_app_id'];
                $payment = new WechatPayUtil($init_config);
                $pay_info = $payment->h5Pay(
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount,
                    ''
                );

                break;

            default:
                throw new \Exception('支付类型错误');
        }

        return [
            'payment' => $pay_info,
            'pay_form' => $pay_form_enum->value,
        ];
    }
}
