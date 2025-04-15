<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\Manage\TransactionResourceCollection;
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
        $status = $request->get('status', null);
        $paid_start_time = $request->get('paid_start_time', null);
        $paid_end_time = $request->get('paid_end_time', null);
        $number = (int) $request->get('number', 10);
        $list = Transaction::query()
            ->with(['typeInfo:id,no', 'user:id,user_name', 'payment:id,name'])
            ->latest()
            ->when($transaction_no, fn (Builder $query) => $query->where('transaction_no', $transaction_no))
            ->when($transaction_type, fn (Builder $query) => $query->where('transaction_type', $transaction_type))
            ->when(is_numeric($status), fn (Builder $query) => $query->where('status', $status))
            ->when($type === 'order', fn (Builder $query) => $query->where('type', Order::class))
            ->when($order_no, function (Builder $query) use ($order_no) {
                $query->whereHasMorph('typeInfo', Order::class, fn (Builder $query) => $query->where('no', $order_no));
            })
            ->when($user_name, function (Builder $query) use ($user_name) {
                $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%"));
            })
            ->when($paid_start_time, fn (Builder $query) => $query->where('paid_at', '>=', $paid_start_time))
            ->when($paid_end_time, fn (Builder $query) => $query->where('paid_at', '<=', $paid_end_time))
            ->paginate($number);

        return $this->success(new TransactionResourceCollection($list));
    }
}
