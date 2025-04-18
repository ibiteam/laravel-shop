<?php

namespace App\Http\Controllers\Manage;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Enums\PayPrefixEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Dao\TransactionDao;
use App\Http\Resources\Manage\TransactionResourceCollection;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Pay\PayService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends BaseController
{
    /**
     * 退款列表.
     */
    public function index(Request $request): JsonResponse
    {
        // 交易记录
        $transaction_no = $request->get('transaction_no', null);
        $type = $request->get('type', null);
        $order_sn = $request->get('order_sn', null);
        $user_name = $request->get('user_name', null);
        $transaction_type = $request->get('transaction_type', null);
        $status = $request->get('status', null);
        $paid_start_time = $request->get('paid_start_time', null);
        $paid_end_time = $request->get('paid_end_time', null);
        $number = (int) $request->get('number', 10);
        $list = Transaction::query()
            ->with(['typeInfo', 'user:id,user_name', 'payment:id,name', 'parent'])
            ->latest()
            ->latest('id')
            ->when($transaction_no, fn (Builder $query) => $query->where('transaction_no', $transaction_no))
            ->when($transaction_type, fn (Builder $query) => $query->where('transaction_type', $transaction_type))
            ->when(is_numeric($status), fn (Builder $query) => $query->where('status', $status))
            ->when($type === 'order', fn (Builder $query) => $query->where('type', Order::class))
            ->when($order_sn, function (Builder $query) use ($order_sn) {
                $query->whereHasMorph('typeInfo', Order::class, fn (Builder $query) => $query->where('order_sn', $order_sn));
            })
            ->when($user_name, function (Builder $query) use ($user_name) {
                $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%"));
            })
            ->when($paid_start_time, fn (Builder $query) => $query->where('paid_at', '>=', $paid_start_time))
            ->when($paid_end_time, fn (Builder $query) => $query->where('paid_at', '<=', $paid_end_time))
            ->paginate($number);

        return $this->success(new TransactionResourceCollection($list));
    }

    /**
     * 申请退款.
     *
     * @throws \Throwable
     */
    public function refund(Request $request, TransactionDao $transaction_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'reason' => 'nullable|string|max:50',
            ], [], [
                'id' => '交易记录ID',
                'reason' => '退款原因',
            ]);
            $transaction = Transaction::query()->with(['payment', 'typeInfo'])->whereId($validated['id'])->first();

            if (! $transaction instanceof Transaction) {
                throw new BusinessException('交易记录不存在');
            }

            if ($transaction->status !== Transaction::STATUS_SUCCESS) {
                throw new BusinessException('交易记录状态异常');
            }

            if ($transaction->transaction_type !== Transaction::TRANSACTION_TYPE_PAY) {
                throw new BusinessException('交易记录类型异常');
            }

            $payment = $transaction->payment;

            if (! $payment instanceof Payment) {
                throw new BusinessException('交易记录支付方式不存在');
            }

            if ($payment->alias !== PaymentEnum::WECHAT->value) {
                throw new BusinessException('当前支付方式不支持退款');
            }
            // 负数 or 0
            $old_refund_amount = $transaction->children()->where('status', Transaction::STATUS_SUCCESS)->sum('amount');

            $refund_amount = bcadd($transaction->amount, $old_refund_amount, 2);

            if ($refund_amount <= 0) {
                throw new BusinessException('交易记录已退款完成');
            }
            $out_refund_no = $transaction_dao->generateTransactionNo(PayPrefixEnum::MANAGE_REFUND->value);

            $payment = $transaction->payment;

            $order = $transaction->typeInfo;

            if ($order instanceof Order) {
                // 更新订单状态
                if (! $order->update([
                    'order_status' => OrderStatusEnum::CANCELLED->value,
                    'pay_status' => PayStatusEnum::PAY_WAIT->value,
                    'ship_status' => ShippingStatusEnum::UNSHIPPED->value,
                    'paid_at' => null,
                    'shipped_at' => null,
                    'received_at' => null,
                ])) {
                    throw new BusinessException('取消订单失败');
                }
            }

            PayService::init($payment->alias)->refund(
                $transaction,
                $out_refund_no,
                $payment,
                $refund_amount,
                $validated['reason'] ?? ''
            );

            admin_operation_log("对交易流水号记录：【{$transaction->transaction_no}】申请退款");

            return $this->success('已提交微信支付退款申请，请耐心等待退款结果');
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
