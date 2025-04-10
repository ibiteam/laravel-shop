<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\AdminUser;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\OrderDetail;
use App\Models\ShipCompany;
use Illuminate\Support\Collection;

class OrderDeliveryDao
{
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
            'delivery_no' => 'D'.get_flow_sn(),
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
}
