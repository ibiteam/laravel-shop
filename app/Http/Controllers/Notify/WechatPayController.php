<?php

namespace App\Http\Controllers\Notify;

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Enums\PayStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Dao\PaymentDao;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Transaction;
use App\Utils\Wechat\WechatPayUtil;
use Carbon\Carbon;
use EasyWeChat\Kernel\ServerResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class WechatPayController extends Controller
{
    /**
     * 退款回调.
     */
    public function notifyRefund(Request $request, PaymentDao $payment_dao): ServerResponse|ResponseInterface
    {
        try {
            // 支付回调
            $payment = $payment_dao->getInfoByAlias(PaymentEnum::WECHAT);

            if (! $payment instanceof Payment) {
                throw new BusinessException('Not Fount Payment');
            }
            $wechat_pay_util = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);

            $message = $wechat_pay_util->server()->getRequestMessage();

            Log::info('微信支付申请退款回调信息：'.$message);

            if (! isset($message['refund_status']) || $message['refund_status'] !== 'SUCCESS') {
                throw new BusinessException('Result Code Fail');
            }
            // 请求微信查询支付结果
            $order_message = $wechat_pay_util->queryRefundOrder($message['out_refund_no']);

            Log::info('微信支付申请退款回调信息->请求微信获取申请退款订单信息：'.json_encode($order_message, JSON_UNESCAPED_UNICODE));

            if (! isset($order_message['status']) || $order_message['status'] !== 'SUCCESS') {
                throw new BusinessException('Query Order Trade State Fail');
            }
            // 退款处理
            $transaction = Transaction::query()->whereTransactionType(Transaction::TRANSACTION_TYPE_REFUND)->whereTransactionNo($order_message['out_refund_no'])->whereStatus(Transaction::STATUS_WAIT)->first();

            if ($transaction instanceof Transaction) {
                $transaction->update(['status' => Transaction::STATUS_SUCCESS, 'paid_at' => now()->toDateTimeString()]);
            }

            return $wechat_pay_util->server()->serve();
        } catch (BusinessException $business_exception) {
            return ServerResponse::make(new Response(
                200,
                [],
                strval(json_encode(['code' => 'FAIL', 'message' => $business_exception->getMessage()], JSON_UNESCAPED_UNICODE))
            ));
        } catch (\Throwable $throwable) {
            Log::error('wechat notify refund throwable:'.$throwable->getMessage(), $throwable->getTrace());

            return ServerResponse::make(new Response(
                200,
                [],
                strval(json_encode(['code' => 'FAIL', 'message' => 'data error'], JSON_UNESCAPED_UNICODE))
            ));
        }
    }

    /**
     * 支付回调.
     */
    public function notifyPay(Request $request, PaymentDao $payment_dao): ServerResponse|ResponseInterface
    {
        try {
            // 支付回调
            $payment = $payment_dao->getInfoByAlias(PaymentEnum::WECHAT);

            if (! $payment instanceof Payment) {
                throw new BusinessException('Not Fount Payment');
            }
            $wechat_pay_util = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);

            $message = $wechat_pay_util->server()->getRequestMessage();

            Log::info('微信支付回调信息：'.$message);

            if (! isset($message['trade_state']) || $message['trade_state'] !== 'SUCCESS') {
                throw new BusinessException('Result Code Fail');
            }
            // 请求微信查询支付结果
            $order_message = $wechat_pay_util->queryOrder($message['out_trade_no']);

            Log::info('微信支付回调信息->请求微信获取订单信息：'.json_encode($order_message, JSON_UNESCAPED_UNICODE));

            if (! isset($order_message['trade_state']) || $order_message['trade_state'] !== 'SUCCESS') {
                throw new BusinessException('Query Order Trade State Fail');
            }

            // 判断是否为订单支付
            if (str_starts_with($order_message['out_trade_no'], 'order_')) {
                $this->notifyOrderPayInfo($order_message);
            }

            return $wechat_pay_util->server()->serve();
        } catch (BusinessException $business_exception) {
            return ServerResponse::make(new Response(
                200,
                [],
                strval(json_encode(['code' => 'FAIL', 'message' => $business_exception->getMessage()], JSON_UNESCAPED_UNICODE))
            ));
        } catch (\Throwable $throwable) {
            Log::error('wechat notify pay throwable:'.$throwable->getMessage(), $throwable->getTrace());

            return ServerResponse::make(new Response(
                200,
                [],
                strval(json_encode(['code' => 'FAIL', 'message' => 'data error'], JSON_UNESCAPED_UNICODE))
            ));
        }
    }

    /**
     * 处理订单支付回调.
     *
     * @throws Throwable
     */
    private function notifyOrderPayInfo(array $order_message): void
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::query()->whereTransactionType(Transaction::TRANSACTION_TYPE_PAY)->whereTransactionNo($order_message['out_trade_no'])->whereStatus(Transaction::STATUS_WAIT)->first();

            if (! $transaction instanceof Transaction) {
                throw new \Exception('未找到支付流水记录');
            }
            $paid_at = Carbon::make($order_message['success_time'])->toDateTimeString();

            $transaction->update(['status' => Transaction::STATUS_SUCCESS, 'paid_at' => $paid_at]);

            // 查询订单信息
            $order = Order::query()->with('detail.goods')->whereId($transaction->type_id)->first();

            if (! $order instanceof Order) {
                throw new \Exception('未找到订单信息');
            }
            $order->money_paid += $transaction->amount;
            $order->paid_at = $paid_at;
            $order->pay_status = PayStatusEnum::PAYED;

            if ($order->money_paid != $order->order_amount) {
                Log::error("订单编号：{$order->order_sn}，支付金额与订单总金额不一致，请前往后台查看！");
            }

            $order->save();

            // 判断是否为支付减少库存
            $order->detail->each(function (OrderDetail $order_detail) {
                if ($order_detail->goods?->type === Goods::TYPE_PAY_ORDER) {
                    if ($order_detail->goods_sku_id > 0) {
                        $goods_sku = GoodsSku::query()->whereId($order_detail->goods_sku_id)->first();

                        if ($goods_sku instanceof GoodsSku) {
                            $goods_sku->decrementStock($order_detail->goods_number);
                        }
                    }

                    $order_detail->goods->decrementStock($order_detail->goods_number);
                }
            });
            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            Log::error('微信支付回调-订单交易流水处理异常：'.$throwable->getMessage());
        }
    }
}
