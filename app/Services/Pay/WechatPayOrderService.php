<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Enums\RouterEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Dao\TransactionDao;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShopConfig;
use App\Models\WechatUser;
use App\Services\RouterService;
use App\Utils\Wechat\WechatPayUtil;

class WechatPayOrderService implements PayOrderInterface
{
    /**
     * 订单支付.
     *
     * @throws BusinessException
     * @throws WeChatPayException
     */
    public function orderPay(Order $order, Payment $payment, PayFormEnum $pay_form_enum): array
    {
        $wechat_pay_util = new WechatPayUtil($payment->config, $pay_form_enum);

        // 创建支付流水记录
        $transaction = app(TransactionDao::class)->storeByOrder($order, $payment, $pay_form_enum->getLabel());

        switch ($pay_form_enum) {
            case PayFormEnum::PAY_FORM_APP:
                $pay_info = $wechat_pay_util->appPay(
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount
                );

                break;

            case PayFormEnum::PAY_FORM_WECHAT:
            case PayFormEnum::PAY_FORM_MINI:
                $openid = WechatUser::query()->whereUserId($order->user_id)->value('openid') ?: false;
                $pay_info = $wechat_pay_util->jsPay(
                    $openid,
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount
                );

                break;

            case PayFormEnum::PAY_FORM_H5:
                $pay_info = $wechat_pay_util->h5Pay(
                    shop_config(ShopConfig::SHOP_NAME).'订单支付',
                    $transaction->transaction_no,
                    $order->order_amount,
                    app(RouterService::class)->getRouterPath(RouterEnum::ORDER_SUCCESS)
                );

                break;

            default:
                throw new BusinessException('支付类型错误');
        }

        return [
            'payment' => $pay_info,
            'pay_form' => $pay_form_enum->value,
        ];
    }
}
