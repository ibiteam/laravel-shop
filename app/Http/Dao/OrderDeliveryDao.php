<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\AdminUser;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\OrderDetail;
use App\Models\ShipCompany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderDeliveryDao
{
    /**
     * 根据订单获取发货列表(分页).
     */
    public function getListByOrder(string $order_sn, int $page = 1, int $number = 10): LengthAwarePaginator
    {
        $order_id = Order::query()->whereOrderSn($order_sn)->first()->id ?? 0;

        $list = OrderDelivery::query()
            ->with(['order:id,order_sn', 'shipCompany:id,name', 'adminUser:id,nickname'])
            ->whereOrderId($order_id)
            ->orderByDesc('shipped_at')
            ->orderByDesc('created_at')
            ->paginate($number, page: $page);
        $list->getCollection()->transform(function (OrderDelivery $order_delivery) {
            return [
                'id' => $order_delivery->id,
                'delivery_no' => $order_delivery->delivery_no,
                'order_sn' => $order_delivery->order->order_sn,
                'ship_company_name' => $order_delivery->shipCompany?->name,
                'ship_no' => $order_delivery->ship_no,
                'status' => $order_delivery->status,
                'shipped_at' => $order_delivery->shipped_at?->format('Y-m-d H:i:s'),
                'received_at' => $order_delivery->received_at?->format('Y-m-d H:i:s'),
                'remark' => $order_delivery->remark,
            ];
        });

        return $list;
    }

    /**
     * 根据订单添加发货.
     *
     * @throws BusinessException
     */
    public function storeByOrder(Order $order, AdminUser $admin_user, ShipCompany $ship_company, array $params): OrderDelivery
    {
        $data = $order->detail->map(fn (OrderDetail $order_detail) => ['order_detail_id' => $order_detail->id, 'send_number' => $order_detail->goods_number]);

        if ($data->isEmpty()) {
            throw new BusinessException('订单明细为空');
        }

        $order_delivery = OrderDelivery::query()->create([
            'delivery_no' => $this->generateDeliveryNo(),
            'order_id' => $order->id,
            'ship_company_id' => $ship_company->id,
            'ship_no' => $params['ship_no'],
            'status' => OrderDelivery::STATUS_WAIT,
            'shipped_at' => $params['shipped_at'] ?? now()->toDateTimeString(),
            'remark' => $params['remark'] ?? '',
            'admin_user_id' => $admin_user->id,
        ]);
        $order_delivery->items()->createMany($data);

        return $order_delivery;
    }

    /**
     * 根据订单删除发货单.
     *
     *
     * @throws BusinessException
     */
    public function destroyByOrder(Order $order): Collection
    {
        // 删除发货单明细
        $order_delivery_ids = OrderDelivery::query()->where('order_id', $order->id)->pluck('id');

        if ($order_delivery_ids->isEmpty()) {
            throw new BusinessException('未找到发货信息');
        }
        OrderDeliveryItem::query()->whereIn('order_delivery_id', $order_delivery_ids)->delete();
        // 删除发货单
        OrderDelivery::query()->whereIn('id', $order_delivery_ids)->delete();

        return $order_delivery_ids;
    }

    /**
     * 生成发货单号.
     */
    public function generateDeliveryNo(): string
    {
        return 'D'.get_flow_sn();
    }
}
