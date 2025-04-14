<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Http\Resources\TransactionResourceCollection;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    public function index(Request $request)
    {
        // 交易记录
        $transaction_no = $request->get('transaction_no', null);
        $type = $request->get('type', null);
        $order_no = $request->get('order_no', null);
        $user_name = $request->get('user_name', null);
        $transaction_type = $request->get('transaction_type', null);
        $status = $request->get('status', -1);
        $payment_id = $request->get('payment_id', null);
        $number = (int) $request->get('number', 10);
        $list = Transaction::query()
            ->with(['typeInfo:id,no', 'user:id,user_name', 'payment:id,name'])
            ->latest()
            ->when($transaction_no, fn (Builder $query) => $query->where('transaction_no', $transaction_no))
            ->when($transaction_type, fn (Builder $query) => $query->where('transaction_type', $transaction_type))
            ->when($payment_id, fn (Builder $query) => $query->where('payment_id', $payment_id))
            ->when($status >= 0, fn (Builder $query) => $query->where('status', $status))
            ->when($type === 'order', fn (Builder $query) => $query->where('type', Order::class))
            ->when($order_no, function (Builder $query) use ($order_no) {
                $query->whereHasMorph('typeInfo', Order::class, fn (Builder $query) => $query->where('no', $order_no));
            })
            ->when($user_name, function (Builder $query) use ($user_name) {
                $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%"));
            })
            ->paginate($number);

        return $this->success(new TransactionResourceCollection($list));
    }
}
