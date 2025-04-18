<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Enums\RouterEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Dao\TransactionDao;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ShopConfig;
use App\Models\Transaction;
use App\Models\WechatUser;
use App\Services\RouterService;
use App\Utils\Wechat\WechatPayUtil;

class WechatPayService implements PayInterface
{
    /**
     * 订单支付.
     *
     * @throws BusinessException
     * @throws WeChatPayException
     */
    public function orderPay(Order $order, Payment $payment, PayFormEnum $pay_form_enum): array
    {
        if ($payment->alias !== PaymentEnum::WECHAT->value) {
            throw new BusinessException('支付方式不匹配');
        }

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
                    app(RouterService::class)->getRouterPath(RouterEnum::ORDER_SUCCESS->value)
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

    /**
     * 退款.
     *
     * @param Transaction $parent_transaction    父级流水
     * @param string      $refund_transaction_no 退款流水号
     * @param Payment     $payment               支付方式
     * @param float|int   $refund_amount         退款金额
     * @param string      $reason                退款原因
     *
     * @throws BusinessException
     * @throws WeChatPayException
     */
    public function refund(Transaction $parent_transaction, string $refund_transaction_no, Payment $payment, float|int $refund_amount, string $reason): array
    {
        if ($parent_transaction->payment_id !== $payment->id || $payment->alias !== PaymentEnum::WECHAT->value) {
            throw new BusinessException('支付方式不匹配');
        }

        $wechat_pay_util = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);

        $wechat_response = $wechat_pay_util->refundOrder(
            $parent_transaction->transaction_no,
            $refund_transaction_no,
            $refund_amount,
            $refund_amount,
            $reason
        );
        $parent_transaction->update(['can_refund' => false]);

        switch ($wechat_response['status'] ?? '') {
            case 'PROCESSING': // 退款处理中
                $transaction = app(TransactionDao::class)->storeByParentTransaction($parent_transaction, $refund_transaction_no, $refund_amount, remark: $reason);

                if (! $transaction) {
                    throw new BusinessException('退款生成流水失败');
                }

                break;

            case 'SUCCESS': // 退款成功
                $transaction = app(TransactionDao::class)->storeByParentTransaction($parent_transaction, $refund_transaction_no, $refund_amount, Transaction::STATUS_SUCCESS, $reason);

                if (! $transaction) {
                    throw new BusinessException('退款生成流水失败');
                }

                break;

            case 'CLOSED': // 退款关闭
                throw new BusinessException('退款关闭，请联系管理员');

            case 'ABNORMAL': // 退款异常
                throw new BusinessException('退款异常，退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台-交易中心，手动处理此笔退款');

            default:
                throw new BusinessException('退款状态异常，请联系管理员');
        }

        return [
            'wechat_response' => $wechat_response,
            'transaction' => $transaction,
        ];
    }
}
