<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ApiOrderResourceCollection;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public const SEARCH_ALL = 'all'; // 全部
    public const SEARCH_NOT_PAY = 'not_pay'; // 未付款
    public const SEARCH_WAIT_SHIP = 'wait_ship'; // 待发货
    public const SEARCH_WAIT_RECEIVE = 'wait_receive'; // 待收货
    public const SEARCH_SUCCESS = 'success'; // 已完成

    public function index(Request $request)
    {
        $keywords = $request->get('keywords');
        $type = $request->get('type', self::SEARCH_ALL);
        $number = (int) $request->get('number', 10);
        $current_user = $this->user();
        $order = Order::query()
            ->withCount('detail')
            ->with(['detail', 'evaluate', 'orderDelivery', 'orderDelivery.shipCompany'])
            ->latest()
            ->whereUserId($current_user->id)
            ->when(! is_null($keywords), fn (Builder $query) => $query->whereLike('no', "%$keywords%"))
            ->when($type === self::SEARCH_NOT_PAY, function (Builder $query) {
                $query
                    ->where('order_status', OrderStatusEnum::CONFIRMED)
                    ->where('pay_status', PayStatusEnum::PAY_WAIT);
            })
            ->when($type === self::SEARCH_WAIT_SHIP, function (Builder $query) {
                $query
                    ->where('order_status', OrderStatusEnum::CONFIRMED)
                    ->where('pay_status', PayStatusEnum::PAYED)
                    ->where('ship_status', ShippingStatusEnum::UNSHIPPED);
            })
            ->when($type === self::SEARCH_WAIT_RECEIVE, function (Builder $query) {
                $query
                    ->where('order_status', OrderStatusEnum::CONFIRMED)
                    ->where('pay_status', PayStatusEnum::PAYED)
                    ->where('ship_status', ShippingStatusEnum::SHIPPED);
            })
            ->when($type === self::SEARCH_SUCCESS, function (Builder $query) {
                $query
                    ->where('order_status', OrderStatusEnum::CONFIRMED)
                    ->where('pay_status', PayStatusEnum::PAYED)
                    ->where('ship_status', ShippingStatusEnum::RECEIVED);
            })
            ->paginate($number);

        return $this->success(new ApiOrderResourceCollection($order));
    }
}
