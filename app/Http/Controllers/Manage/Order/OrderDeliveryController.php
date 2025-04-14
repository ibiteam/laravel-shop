<?php

namespace App\Http\Controllers\Manage\Order;

use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\OrderDelivery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrderDeliveryController extends BaseController
{
    public function index(Request $request)
    {
        $delivery_no = $request->get('delivery_no', null);
        $order_no = $request->get('order_no', null);
        $created_start_time = $request->get('created_start_time', null);
        $created_end_time = $request->get('created_end_time', null);
        $list = OrderDelivery::query()
            ->latest()
            ->with(['order:id,no', 'shipCompany:id,name', 'adminUser:id,nickname'])
            ->when(! is_null($delivery_no), fn (Builder $query) => $query->where('delivery_no', $delivery_no))
            ->when(! is_null($order_no), fn (Builder $query) => $query->whereHas('order', fn ($query) => $query->where('no', $order_no)))
            ->when(! is_null($created_start_time), fn (Builder $query) => $query->where('shipped_at', '>=', $created_start_time))
            ->when(! is_null($created_end_time), fn (Builder $query) => $query->where('shipped_at', '<=', $created_end_time))
            ->paginate();

        return $this->success(new CommonResourceCollection($list));
    }
}
