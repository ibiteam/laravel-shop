<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Enums\PayStatusEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDao;
use App\Http\Dao\PaymentDao;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Pay\WechatPayOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PayController extends BaseController
{
    /**
     * 收银台.
     */
    public function index(Request $request, OrderDao $order_dao, PaymentDao $payment_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '订单编号',
            ]);
            $current_user = $this->user();
            $order = $order_dao->getInfoByNo($validated['no'], $current_user->id);

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if ($order->pay_status === PayStatusEnum::PAYED->value) {
                throw new BusinessException('订单已支付');
            }
            // 获取可用的支付方式
            $payments = $payment_dao->getListByEnabled()->map(function (Payment $payment) use ($order) {
                $can_use = true;

                if ($payment->limit >= 0 && $order->order_amount > $payment->limit) {
                    $can_use = false;
                }

                return [
                    'name' => $payment->name,
                    'description' => $payment->description,
                    'icon' => $payment->icon,
                    'alias' => $payment->alias,
                    'is_recommend' => $payment->is_recommend,
                    'sort' => $payment->sort,
                    'can_use' => $can_use,
                ];
            });

            if ($payments->isEmpty()) {
                throw new BusinessException('暂无可用的支付方式');
            }

            return $this->success([
                'order' => [
                    'no' => $order->order_sn,
                    'order_amount' => $order->order_amount,
                    'created_at' => $order->created_at->toDateTimeString(),
                ],
                'payments' => $payments,
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    public function wechatPay(Request $request, OrderDao $order_dao, PaymentDao $payment_dao, WechatPayOrderService $wechat_pay_order_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
                'pay_form' => 'required|string',
            ], [], [
                'no' => '订单编号',
                'pay_form' => '支付类型',
            ]);
            $wechat_pay_form_enum = PayFormEnum::formSource($validated['pay_form']);
            $current_user = $this->user();
            $order = $order_dao->getInfoByNo($validated['no'], $current_user->id);

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if ($order->pay_status === PayStatusEnum::PAYED->value) {
                throw new BusinessException('订单已支付');
            }
            // 获取可用的支付方式
            $payment = $payment_dao->getInfoByAlias(PaymentEnum::WECHAT);

            if (! $payment instanceof Payment || ! $payment->is_enabled) {
                throw new BusinessException('该支付方式暂不可用');
            }
            $data = $wechat_pay_order_service->orderPay($order, $payment, $wechat_pay_form_enum);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (WeChatPayException $we_chat_pay_exception) {
            return $this->error($we_chat_pay_exception->getMessage(), $we_chat_pay_exception->getCodeEnum());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
