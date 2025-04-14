<?php

namespace App\Http\Controllers\Manage\Order;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Dao\OrderDeliveryDao;
use App\Http\Dao\ShipCompanyDao;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDetail;
use App\Models\ShipCompany;
use App\Utils\ExcelUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderDeliveryController extends BaseController
{
    /**
     * 发货列表.
     */
    public function index(Request $request): JsonResponse
    {
        $delivery_no = $request->get('delivery_no', null);
        $order_no = $request->get('order_no', null);
        $created_start_time = $request->get('created_start_time', null);
        $created_end_time = $request->get('created_end_time', null);
        $number = (int) $request->get('number', 10);
        $list = OrderDelivery::query()
            ->latest()
            ->with(['order:id,no', 'shipCompany:id,name', 'adminUser:id,nickname'])
            ->when(! is_null($delivery_no), fn (Builder $query) => $query->where('delivery_no', $delivery_no))
            ->when(! is_null($order_no), fn (Builder $query) => $query->whereHas('order', fn ($query) => $query->where('no', $order_no)))
            ->when(! is_null($created_start_time), fn (Builder $query) => $query->where('shipped_at', '>=', $created_start_time))
            ->when(! is_null($created_end_time), fn (Builder $query) => $query->where('shipped_at', '<=', $created_end_time))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 导入发货.
     */
    public function import(Request $request): JsonResponse
    {
        $file = $request->file('import_file');

        if (! $file || ! $file->isValid()) {
            return $this->error('文件错误');
        }

        if ($file->getClientOriginalExtension() != 'xlsx') {
            return $this->error('文件格式错误');
        }
        $current_user = $this->adminUser();

        $order_delivery_dao = app(OrderDeliveryDao::class);

        try {
            $res = ExcelUtil::import($file, function ($data) use ($current_user, $order_delivery_dao) {
                $error_data = [];
                $import_data = [];

                $success_number = 0;

                if (count($data) > 1000) {
                    throw new BusinessException('导入数量限制 单次最多导入 1000 条数据');
                }

                $ship_companies = app(ShipCompanyDao::class)->getListByEnable()->mapWithKeys(fn (ShipCompany $ship_company) => [$ship_company->name => $ship_company->id])->toArray();

                foreach ($data as $key => $datum) {
                    if ($key === 0) {
                        continue;
                    }
                    $order_no = $datum[0];
                    $goods_name = $datum[1] ?? '';
                    $send_number = $datum[2] ?? 0;
                    $ship_company_name = $datum[3];
                    $ship_no = $datum[4];
                    $shipped_at = $datum[5];
                    $remark = $datum[6] ?? '';

                    $line = $key + 1;

                    if (! $order_no || ! $ship_company_name || ! $ship_no || ! $shipped_at) {
                        $error_data[] = ['line' => $line, 'message' => '数据错误缺少必填数据'];

                        continue;
                    }
                    $ship_company_id = $ship_companies[$ship_company_name] ?? 0;

                    if ($ship_company_id === 0) {
                        $error_data[] = ['line' => $line, 'message' => '快递公司错误'];

                        continue;
                    }

                    if (date('Y-m-d H:i:s', strtotime($shipped_at)) !== $shipped_at) {
                        $error_data[] = ['line' => $line, 'message' => '发货时间格式错误'];

                        continue;
                    }

                    $import_data[] = [
                        'line' => $line,
                        'order_no' => $order_no,
                        'goods_name' => $goods_name,
                        'send_number' => $send_number,
                        'ship_company_id' => $ship_company_id,
                        'ship_no' => $ship_no,
                        'shipped_at' => $shipped_at,
                        'remark' => $remark,
                    ];
                }
                $orders = Order::query()
                    ->with('detail:id,order_id,goods_name,goods_number')
                    ->where('order_status', OrderStatusEnum::CONFIRMED)
                    ->where('pay_status', PayStatusEnum::PAYED)
                    ->where('ship_status', ShippingStatusEnum::UNSHIPPED)
                    ->whereIn('no', array_column($import_data, 'order_no'))
                    ->select(['id', 'no', 'order_status', 'pay_status', 'ship_status'])
                    ->get()
                    ->keyBy('no');

                foreach ($import_data as $import_datum) {
                    $order = $orders[$import_datum['order_no']] ?? null;

                    if (! $order instanceof Order) {
                        $error_data[] = ['line' => $import_datum['line'], 'message' => '未查询到已确认已付款未发货订单信息'];

                        continue;
                    }
                    $order_ship_status = ShippingStatusEnum::SHIPPED;

                    if ($import_datum['goods_name']) {
                        /**
                         * 导入信息有商品名称时
                         * 查询订单商品信息,没有查询到则直接跳过,当发货数量存在时判断是否发货数量大于订单商品数量;当发货数量为0时,发货数量等于订单商品数量.
                         */
                        $order_detail = $order->detail->where('goods_name', $import_datum['goods_name'])->first();

                        if (! $order_detail instanceof OrderDetail) {
                            $error_data[] = ['line' => $import_datum['line'], 'message' => '未查询到订单商品，商品名称'.$import_datum['goods_name']];

                            continue;
                        }

                        if ($import_datum['send_number'] > 0) {
                            // 部分发货
                            if ($import_datum['send_number'] < $order_detail->goods_number) {
                                $order_ship_status = ShippingStatusEnum::PART;
                            }

                            if ($import_datum['send_number'] > $order_detail->goods_number) {
                                $error_data[] = ['line' => $import_datum['line'], 'message' => '商品名称'.$import_datum['goods_name'].' 发货数量超过订单商品数量'];

                                continue;
                            }
                            $order_delivery_items = [
                                ['order_detail_id' => $order_detail->id, 'send_number' => $import_datum['send_number']],
                            ];
                        } else {
                            $order_delivery_items = [
                                ['order_detail_id' => $order_detail->id, 'send_number' => $order_detail->goods_number],
                            ];
                        }
                    } else {
                        /**
                         * 导入信息没有商品名称时
                         * 先判断发货数量存在,存在 则获取订单商品数量,当订单商品数量大于1时,提示商品名称未填写;当订单商品数量等于1时,判断订单商品发货数量是否大于订单商品购买数量.
                         * 发货数量不存在时,直接默认发货全部订单商品
                         */
                        if ($import_datum['send_number'] > 0) {
                            if ($order->detail->count() > 1) {
                                $error_data[] = ['line' => $import_datum['line'], 'message' => '发货数量大于0但商品名称未填写且订单商品数量大于1'];

                                continue;
                            }
                            $order_detail = $order->detail->first();

                            if (! $order_detail instanceof OrderDetail) {
                                $error_data[] = ['line' => $import_datum['line'], 'message' => '未查询到订单商品'];

                                continue;
                            }

                            // 部分发货
                            if ($import_datum['send_number'] < $order_detail->goods_number) {
                                $order_ship_status = ShippingStatusEnum::PART;
                            }

                            if ($import_datum['send_number'] > $order_detail->goods_number) {
                                $error_data[] = ['line' => $import_datum['line'], 'message' => '发货数量超过订单商品数量'];

                                continue;
                            }
                            $order_delivery_items = [
                                ['order_detail_id' => $order_detail->id, 'send_number' => $import_datum['send_number']],
                            ];
                        } else {
                            // 默认发货所有商品
                            $order_delivery_items = $order->detail->map(function (OrderDetail $order_detail) {
                                return ['order_detail_id' => $order_detail->id, 'send_number' => $order_detail->goods_number];
                            })->toArray();
                        }
                    }

                    if (empty($order_delivery_items)) {
                        $error_data[] = ['line' => $import_datum['line'], 'message' => '未查询到发货订单商品'];

                        continue;
                    }

                    $order_delivery = OrderDelivery::query()->create([
                        'delivery_no' => $order_delivery_dao->generateDeliveryNo(),
                        'order_id' => $order->id,
                        'ship_company_id' => $import_datum['ship_company_id'],
                        'ship_no' => $import_datum['ship_no'],
                        'status' => OrderDelivery::STATUS_WAIT,
                        'shipped_at' => $import_datum['shipped_at'],
                        'remark' => $import_datum['remark'],
                        'admin_user_id' => $current_user->id,
                    ]);

                    $order_delivery->items()->createMany($order_delivery_items);
                    $order->update(['ship_status' => $order_ship_status, 'shipped_at' => $import_datum['shipped_at']]);

                    $success_number++;
                }

                return [
                    'success_number' => $success_number,
                    'error_data' => $error_data,
                ];
            });

            return $this->success($res);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('导入发货信息失败！');
        }
    }
}
