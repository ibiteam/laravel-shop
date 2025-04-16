<?php

namespace App\Http\Controllers\Manage;

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\WeChatPayException;
use App\Http\Dao\TransactionDao;
use App\Http\Resources\Manage\TransactionResourceCollection;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Utils\Wechat\WechatPayUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['typeInfo', 'user:id,user_name', 'payment:id,name'])
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
            $transaction = Transaction::query()->with('payment')->whereId($validated['id'])->first();

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
            $old_refund_amount = $transaction->children()->sum('amount');

            if ($old_refund_amount == $transaction->amount) {
                throw new BusinessException('交易记录已退款完成');
            }
            $refund_amount = $transaction->amount - $old_refund_amount;
            // 请求微信退款
            $wechat_pay_util = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);

            $out_refund_no = $transaction_dao->generateTransactionNo(config('app.manage_prefix').'_'.'refund');

            $reason = $validated['reason'] ?? '';

            $wechat_response = $wechat_pay_util->refundOrder($transaction->transaction_no, $out_refund_no, $refund_amount, $transaction->amount, $reason);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (WeChatPayException $we_chat_pay_exception) {
            return $this->error($we_chat_pay_exception->getMessage(), $we_chat_pay_exception->getCodeEnum());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        DB::beginTransaction();

        try {
            admin_operation_log($this->adminUser(), "针对流水号：【{$transaction->transaction_no}】申请退款");

            $transaction->can_refund = false;
            $transaction->save();

            switch ($wechat_response['status'] ?? '') {
                case 'PROCESSING': // 退款处理中
                    $response_message = '已经提交微信退款申请，请耐心等待~';
                    $transaction_dao->storeByManageRefund($transaction, $out_refund_no, $refund_amount, remark: $reason);

                    break;

                case 'SUCCESS': // 退款成功
                    $response_message = '退款成功';
                    $transaction_dao->storeByManageRefund($transaction, $out_refund_no, $refund_amount, Transaction::STATUS_SUCCESS, remark: $validated['reason']);

                    break;

                case 'CLOSED': // 退款关闭
                    throw new BusinessException('退款关闭，请联系管理员');

                    break;

                case 'ABNORMAL': // 退款异常
                    throw new BusinessException('退款异常，退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台-交易中心，手动处理此笔退款');

                    break;

                default:
                    throw new BusinessException('退款状态异常，请联系管理员');
            }

            DB::commit();

            return $this->success($response_message);
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return $this->error('退款失败');
        }
    }
}
