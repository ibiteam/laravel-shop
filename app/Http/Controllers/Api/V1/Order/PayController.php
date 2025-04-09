<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\PayFormEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PayStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDao;
use App\Http\Dao\PaymentMethodDao;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Pay\WechatPayOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PayController extends BaseController
{
    /**
     * 收银台.
     */
    public function index(Request $request, OrderDao $order_dao, PaymentMethodDao $payment_method_dao): JsonResponse
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
            $payment_methods = $payment_method_dao->getListByEnabled()->map(function (PaymentMethod $payment_method) use ($order) {
                $can_use = true;

                if ($payment_method->limit >= 0 && $order->order_amount > $payment_method->limit) {
                    $can_use = false;
                }

                return [
                    'name' => $payment_method->name,
                    'description' => $payment_method->description,
                    'icon' => $payment_method->icon,
                    'alias' => $payment_method->alias,
                    'is_recommend' => $payment_method->is_recommend,
                    'sort' => $payment_method->sort,
                    'can_use' => $can_use,
                ];
            });

            if ($payment_methods->isEmpty()) {
                throw new BusinessException('暂无可用的支付方式');
            }

            return $this->success([
                'order' => [
                    'no' => $order->no,
                    'order_amount' => $order->order_amount,
                    'created_at' => $order->created_at->toDateTimeString(),
                ],
                'payment_methods' => $payment_methods,
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    public function wechatPay(Request $request, OrderDao $order_dao, PaymentMethodDao $payment_method_dao, WechatPayOrderService $wechat_pay_order_service): JsonResponse
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
            $payment_method = $payment_method_dao->getInfoByAlias(PaymentMethodEnum::WECHAT);

            if (! $payment_method instanceof PaymentMethod || ! $payment_method->is_enabled) {
                throw new BusinessException('该支付方式暂不可用');
            }
            $data = $wechat_pay_order_service->orderPay($order, $payment_method, $wechat_pay_form_enum);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
