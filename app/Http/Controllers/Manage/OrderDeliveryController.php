<?php

namespace App\Http\Controllers\Manage;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\OrderDeliveryDao;
use App\Http\Dao\OrderLogDao;
use App\Http\Dao\ShipCompanyDao;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\OrderDetail;
use App\Models\OrderLog;
use App\Models\ShipCompany;
use App\Utils\ExcelUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderDeliveryController extends BaseController
{
    /**
     * 发货列表.
     */
    public function index(Request $request): JsonResponse
    {
        $delivery_no = $request->get('delivery_no', null);
        $order_sn = $request->get('order_sn', null);
        $created_start_time = $request->get('created_start_time', null);
        $created_end_time = $request->get('created_end_time', null);
        $number = (int) $request->get('number', 10);
        $list = OrderDelivery::query()
            ->latest()
            ->with(['order:id,order_sn', 'shipCompany:id,name', 'adminUser:id,nickname'])
            ->when(! is_null($delivery_no), fn (Builder $query) => $query->where('delivery_no', $delivery_no))
            ->when(! is_null($order_sn), fn (Builder $query) => $query->whereHas('order', fn ($query) => $query->where('order_sn', $order_sn)))
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
        $current_user = get_admin_user();

        $order_delivery_dao = app(OrderDeliveryDao::class);
        $order_log_dao = app(OrderLogDao::class);

        try {
            $res = ExcelUtil::import($file, function ($data) use ($current_user, $order_delivery_dao, $order_log_dao) {
                $error_data = [];
                $import_data = [];
                $success_data = [];

                if (count($data) > 1000) {
                    throw new BusinessException('导入数量限制 单次最多导入 1000 条数据');
                }

                $ship_companies = app(ShipCompanyDao::class)->getListByEnable()->mapWithKeys(fn (ShipCompany $ship_company) => [$ship_company->name => $ship_company->id])->toArray();

                foreach ($data as $key => $datum) {
                    if ($key === 0) {
                        continue;
                    }
                    $order_sn = $datum[0];
                    $goods_name = $datum[1] ?? '';
                    $send_number = $datum[2] ?? 0;
                    $ship_company_name = $datum[3];
                    $ship_no = $datum[4];
                    $shipped_at = $datum[5];
                    $remark = $datum[6] ?? '';

                    $line = $key + 1;

                    if (! $order_sn) {
                        continue;
                    }

                    if (! $ship_company_name || ! $ship_no || ! $shipped_at) {
                        $error_data[] = ['line' => $line, 'message' => '数据错误缺少必填数据'];

                        continue;
                    }
                    $ship_company_id = $ship_companies[$ship_company_name] ?? 0;

                    if ($ship_company_id === 0) {
                        $error_data[] = ['line' => $line, 'message' => '快递公司错误'];

                        continue;
                    }

                    $ship_timestamp = strtotime($shipped_at);

                    if (date('Y-m-d H:i:s', $ship_timestamp) !== $shipped_at) {
                        $error_data[] = ['line' => $line, 'message' => '发货时间格式错误'];

                        continue;
                    }

                    if ($ship_timestamp <= now()->startOfYear()->getTimestamp() || $ship_timestamp >= now()->getTimestamp()) {
                        $error_data[] = ['line' => $line, 'message' => '发货时间错误，请选择今年的发货时间'];

                        continue;
                    }

                    $import_data[] = [
                        'line' => $line,
                        'order_sn' => $order_sn,
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
                    ->where('order_status', OrderStatusEnum::CONFIRMED->value)
                    ->where('pay_status', PayStatusEnum::PAYED->value)
                    ->whereIn('ship_status', [ShippingStatusEnum::UNSHIPPED->value, ShippingStatusEnum::PART->value])
                    ->whereIn('order_sn', array_column($import_data, 'order_sn'))
                    ->select(['id', 'order_sn', 'order_status', 'pay_status', 'ship_status'])
                    ->get()
                    ->keyBy('order_sn');

                foreach ($import_data as $import_datum) {
                    $order = $orders[$import_datum['order_sn']] ?? null;

                    if (! $order instanceof Order) {
                        $error_data[] = ['line' => $import_datum['line'], 'message' => '未查询到已确认已付款未发货订单信息'];

                        continue;
                    }

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
                            // 已发货数量
                            $already_shipped_number = OrderDeliveryItem::query()->whereOrderDetailId($order_detail->id)->sum('send_number');

                            // 判断已发货数量+当前发货数量是否大于订单商品数量
                            if ($already_shipped_number + $import_datum['send_number'] > $order_detail->goods_number) {
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

                            // 已发货数量
                            $already_shipped_number = OrderDeliveryItem::query()->whereOrderDetailId($order_detail->id)->sum('send_number');

                            if ($already_shipped_number + $import_datum['send_number'] > $order_detail->goods_number) {
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
                    // 计算全部已发货数量
                    $order_ship_status = ShippingStatusEnum::SHIPPED;
                    foreach ($order->detail as $order_detail) {
                        $tmp_total_send_number = OrderDeliveryItem::query()->whereOrderDetailId($order_detail->id)->sum('send_number');
                        // 订单商品的总发货量如果小于订单商品的数量直接终止循环
                        if ($tmp_total_send_number < $order_detail->goods_number) {
                            $order_ship_status = ShippingStatusEnum::PART;
                            break;
                        }
                    }
                    $order->update(['ship_status' => $order_ship_status->value, 'shipped_at' => $import_datum['shipped_at']]);
                    $order_log_dao->storeByAdminUser($current_user, $order, '添加发货', OrderLog::TYPE_ADMIN_USER);
                    $success_data[] = $order_delivery->id;
                }

                return [
                    'error_data' => $error_data,
                    'success_data' => $success_data,
                ];
            });

            if (! empty($res['success_data'])) {
                admin_operation_log('导入发货信息：发货单ID'.implode(',', $res['success_data']));
            }

            return $this->success([
                'success_number' => count($res['success_data']),
                'error_number' => count($res['error_data']),
                'error_data' => collect($res['error_data'])->sortBy('line')->values(),
            ]);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('导入发货信息失败！');
        }
    }

    /**
     * 删除发货单.
     *
     * @throws \Throwable
     */
    public function destroy(Request $request, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '发货单ID',
            ]);
            $order_delivery = OrderDelivery::query()->with('order')->whereId($validated['id'])->first();

            if (! $order_delivery instanceof OrderDelivery) {
                throw new BusinessException('发货单不存在');
            }

            if ($order_delivery->status === OrderDelivery::STATUS_SUCCESS) {
                throw new BusinessException('该发货单已完成收货，无法删除');
            }
            // 需要修改订单发货状态 判断是未发货还是部分发货
            $order_ship_status = ShippingStatusEnum::UNSHIPPED;
            $order_shipped_at = null;
            $order_deliveries = OrderDelivery::query()->whereOrderId($order_delivery->order_id)->where('id', '!=', $order_delivery->id)->orderByDesc('shipped_at')->get();
            // todo 重新计算订单发货状态 该订单发货状态不正确

            foreach ($order_deliveries as $key => $order_delivery_datum) {
                if ($key === 0) {
                    $order_shipped_at = $order_delivery_datum->shipped_at;
                }

                if ($order_delivery_datum->status === OrderDelivery::STATUS_WAIT) {
                    $order_ship_status = ShippingStatusEnum::PART;

                    break;
                }
                $order_ship_status = ShippingStatusEnum::SHIPPED;
            }

            $order = $order_delivery->order;

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        $current_user = get_admin_user();

        DB::beginTransaction();

        try {
            if (! $order->update(['ship_status' => $order_ship_status->value, 'shipped_at' => $order_shipped_at])) {
                throw new BusinessException('修改订单发货状态失败');
            }

            if (! $order_delivery->items()->delete()) {
                throw new BusinessException('删除发货单商品失败');
            }

            if (! $order_delivery->delete()) {
                throw new BusinessException('删除发货单失败');
            }

            $order_log_dao->storeByAdminUser($current_user, $order, '调整发货信息', OrderLog::TYPE_ADMIN_USER);

            admin_operation_log("删除发货单,发货单ID：$order_delivery->id");

            DB::commit();

            return $this->success('删除发货单成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return $this->error('删除失败');
        }
    }
}
