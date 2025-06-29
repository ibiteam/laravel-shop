<?php

namespace App\Http\Controllers\Manage;

use App\Enums\ApplyRefundStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Dao\ApplyRefundLogDao;
use App\Http\Dao\OrderLogDao;
use App\Http\Resources\CommonResourceCollection;
use App\Jobs\Order\ApplyRefundJob;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use App\Models\ApplyRefundReason;
use App\Models\ShopConfig;
use App\Services\ExpressService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

// 申请售后/退款
class ApplyRefundController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $user_name = $request->get('user_name', '');
        $goods_name = $request->get('goods_name', '');
        $order_sn = $request->get('order_sn', '');
        $no = $request->get('no', '');
        $status = $request->get('status');
        $type = $request->get('type');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $per_page = (int) $request->get('per_page', 10);

        $data = ApplyRefund::query()
            ->with(['user', 'order', 'orderDetail', 'applyRefundReason'])
            ->when($user_name, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('user_name', 'like', '%'.$user_name.'%')))
            ->when($goods_name, fn ($query) => $query->whereHas('orderDetail', fn ($query) => $query->where('goods_name', 'like', '%'.$goods_name.'%')))
            ->when($order_sn, fn ($query) => $query->whereHas('order', fn ($query) => $query->where('order_sn', 'like', '%'.$order_sn.'%')))
            ->when($no, fn ($query) => $query->where('no', 'like', '%'.$no.'%'))
            ->when(! is_null($status), fn ($query) => $query->where('status', '=', $status))
            ->when(! is_null($type), fn ($query) => $query->where('type', '=', $type))
            ->when($start_time, fn ($query) => $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($end_time))))
            ->orderByDesc('id')->paginate($per_page);
        $data->getCollection()->transform(function (ApplyRefund $apply_refund) {
            return [
                'id' => $apply_refund->id,
                'no' => $apply_refund->no,
                'user_name' => $apply_refund->user?->user_name,
                'goods_name' => $apply_refund->orderDetail?->goods_name,
                'order_sn' => $apply_refund->order?->order_sn,
                'type' => strval($apply_refund->type),
                'status' => $apply_refund->status,
                'money' => $apply_refund->money,
                'integral' => $apply_refund->integral,
                'number' => $apply_refund->number,
                'reason' => $apply_refund->applyRefundReason?->content,
                'description' => $apply_refund->description,
                'certificate' => $apply_refund->certificate,
                'is_revoke' => strval($apply_refund->is_revoke),
                'count' => $apply_refund->count,
                'result' => $apply_refund->result,
                'created_at' => $apply_refund->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $apply_refund->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 详情.
     */
    public function detail(Request $request)
    {
        $id = $request->get('id');

        $apply_refund = ApplyRefund::query()
            ->with('applyRefundLogs', 'applyRefundShip', 'applyRefundShip.shipCompany', 'applyRefundReason')
            ->with(['user' => function ($query) {
                return $query->select('id', 'user_name', 'avatar');
            }])
            ->with(['order' => function ($query) {
                return $query->select('id', 'order_sn', 'user_id', 'created_at');
            }])
            ->with(['orderDetail' => function ($query) {
                return $query->select(['id', 'goods_id', 'goods_name', 'goods_number', 'goods_price', 'goods_amount', 'goods_integral', 'goods_total_integral', 'goods_unit', 'goods_sku_id', 'goods_sku_value'])
                    ->with(['goods' => function ($query) {
                        return $query->select(['id', 'image', 'unit']);
                    }]);
            }])
            ->whereId($id)->first();

        if (! $apply_refund) {
            return $this->error('未找到申请记录');
        }

        return $this->success($this->transformer($apply_refund));
    }

    /**
     * 同意申请（退货退款）.
     */
    public function agreeApply(Request $request, ApplyRefundDao $apply_refund_dao, ApplyRefundLogDao $apply_refund_log_dao, OrderLogDao $order_log_dao)
    {
        $current_user = get_admin_user();

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'money' => 'required|numeric',
                'integral' => 'nullable|numeric',
            ], [], [
                'id' => '申请售后ID',
                'money' => '金额',
                'integral' => '积分',
            ]);

            $id = $validated['id'];
            $money = (float) $validated['money'];
            $integral = (int) ($validated['integral'] ?? 0);

            $apply_refund = ApplyRefund::query()
                ->whereId($id)
                ->whereType(ApplyRefund::TYPE_REFUND_GOODS)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)
                ->first();

            if (! $apply_refund) {
                throw new BusinessException('申请记录不存在');
            }

            // 退款交易检测
            $apply_refund_dao->refundTransactionCheck($apply_refund);

            if ($money && $apply_refund->money != $money) {
                if ($money > $apply_refund->money) {
                    throw new BusinessException('退款金额不能超过申请金额');
                }
                $apply_refund->money = $money;  // 修改退款金额
            }

            if ($integral && $apply_refund->integral != $integral) {
                if ($integral > $apply_refund->integral) {
                    throw new BusinessException('退款积分不能超过申请积分');
                }
                $apply_refund->integral = $integral;    // 修改退款积分
            }

            $buyer_refund_time = intval(shop_config(ShopConfig::BUYER_REFUND_TIME));
            $job_time = Carbon::now()->addDays($buyer_refund_time);

            DB::beginTransaction();

            try {
                $typeMsg = '退货退款';
                // $apply_refund->money = $money; // 修改退款金额(上面有了)
                $apply_refund->job_time = $job_time;
                $apply_refund->status = ApplyRefundStatusEnum::REFUSE_EXAMINE->value;
                $apply_refund->result = "卖家同意了本次{$typeMsg}申请";
                $apply_refund->save();

                $apply_refund_log_dao->addLog($apply_refund, $current_user->user_name, '卖家同意了'.$typeMsg, ApplyRefundLog::TYPE_SELLER);

                $order_log_dao->storeByAdminUser($current_user, $apply_refund->order, "卖家同意了{$typeMsg}");

                admin_operation_log("同意了{$typeMsg}申请记录：{$apply_refund->id}");

                DB::commit();

                // 等待买家操作退货流程
                ApplyRefundJob::dispatch(ApplyRefundStatusEnum::REFUSE_EXAMINE->value, $apply_refund->id, '买家退货超时，退款流程系统自动关闭', ApplyRefundLog::TYPE_BUYER)->delay($job_time);
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('同意申请失败');
            }

            return $this->success('更新成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            Log::error('同意申请异常~'.$throwable);

            return $this->error('同意申请异常');
        }
    }

    /**
     * 关闭申请（已发货）.
     */
    public function closeApply(Request $request, ApplyRefundLogDao $apply_refund_log_dao, OrderLogDao $order_log_dao)
    {
        $current_user = get_admin_user();

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $apply_refund = ApplyRefund::query()->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)
                ->first();

            if (! $apply_refund) {
                throw new BusinessException('申请记录不存在');
            }

            DB::beginTransaction();

            try {
                $apply_refund->status = ApplyRefundStatusEnum::REFUND_CLOSE->value;
                $apply_refund->result = '退款流程已关闭，交易正常进行';
                $apply_refund->save();

                $apply_refund_log_dao->addLog($apply_refund, $current_user->user_name, '卖家已发货，退款流程关闭，交易正常进行', ApplyRefundLog::TYPE_SELLER);

                $order_log_dao->storeByAdminUser($current_user, $apply_refund->order, '卖家已发货，退款流程关闭，交易正常进行');

                admin_operation_log("关闭了退款申请记录：{$apply_refund->id}");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('关闭申请失败');
            }

            return $this->success('关闭成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            Log::error('关闭申请异常~'.$throwable);

            return $this->error('关闭申请异常');
        }
    }

    /**
     * 同意退款（退货退款可修改金额money）.
     */
    public function executeRefund(Request $request, ApplyRefundDao $apply_refund_dao, ApplyRefundLogDao $apply_refund_log_dao, OrderLogDao $order_log_dao)
    {
        $current_user = get_admin_user();

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'money' => 'nullable|numeric',
                'integral' => 'nullable|numeric',
            ], [], [
                'id' => '申请售后ID',
                'money' => '金额',
                'integral' => '积分',
            ]);

            $id = $validated['id'];
            $money = (float) ($validated['money'] ?? 0);
            $integral = (int) ($validated['integral'] ?? 0);

            $apply_refund = ApplyRefund::query()->with(['order', 'orderDetail', 'user', 'applyRefundReason'])->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)
                ->first();

            if (! $apply_refund) {
                throw new BusinessException('申请记录不存在');
            }

            if ($apply_refund->transaction_id > 0) {
                throw new BusinessException('已执行退款，请勿重复操作');
            }

            if ($money && $apply_refund->money != $money) {
                if ($money > $apply_refund->money) {
                    throw new BusinessException('退款金额不能超过申请金额');
                }

                $apply_refund->money = $money;
            }

            if ($integral && $apply_refund->integral != $integral) {
                if ($integral > $apply_refund->integral) {
                    throw new BusinessException('退款积分不能超过申请积分');
                }

                $apply_refund->integral = $integral;
            }

            // 操作退款
            $apply_refund_dao->operationRefund($apply_refund);

            DB::beginTransaction();

            try {
                $apply_refund_log_dao->addLog($apply_refund, $current_user->user_name, '卖家同意了退款', ApplyRefundLog::TYPE_SELLER);

                $order_log_dao->storeByAdminUser($current_user, $apply_refund->order, '卖家同意了退款');

                admin_operation_log("同意了退款申请记录：{$apply_refund->id}");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('执行退款失败');
            }

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            Log::error('执行退款异常~'.$throwable);

            return $this->error('执行退款异常');
        }
    }

    /**
     * 拒绝退款.
     */
    public function refuseRefund(Request $request, ApplyRefundLogDao $apply_refund_log_dao, OrderLogDao $order_log_dao)
    {
        $current_user = get_admin_user();

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'result' => 'required|string',
            ], [], [
                'id' => '申请售后ID',
                'result' => '原因',
            ]);

            $id = $validated['id'];
            $result = $validated['result'];

            $apply_refund = ApplyRefund::query()->whereId($id)->first();

            if (! $apply_refund) {
                throw new BusinessException('申请记录不存在');
            }

            $buyer_change_time = intval(shop_config(ShopConfig::BUYER_CHANGE_TIME));
            $job_time = Carbon::now()->addHours($buyer_change_time);

            DB::beginTransaction();

            try {
                $apply_refund->status = ApplyRefundStatusEnum::REFUSE->value;
                $apply_refund->result = $result;
                $apply_refund->job_time = $job_time;
                $apply_refund->save();

                $apply_refund_log_dao->addLog($apply_refund, $current_user->user_name, '卖家拒绝了退货退款', ApplyRefundLog::TYPE_SELLER);

                $order_log_dao->storeByAdminUser($current_user, $apply_refund->order, '卖家拒绝了退货退款');

                admin_operation_log("拒绝了退款申请记录：{$apply_refund->id}");

                DB::commit();

                // 添加job 等待买家再72小时内操作关闭了流程
                ApplyRefundJob::dispatch(ApplyRefundStatusEnum::REFUSE->value, $apply_refund->id, '买家超时未申请，退款流程自动关闭', ApplyRefundLog::TYPE_SELLER)->delay($job_time);
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('拒绝退款失败');
            }

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            Log::error('拒绝退款异常~'.$throwable);

            return $this->error('拒绝退款异常');
        }
    }

    /**
     * 确认收货.
     */
    public function confirmReceipt(Request $request, ApplyRefundDao $apply_refund_dao, ApplyRefundLogDao $apply_refund_log_dao, OrderLogDao $order_log_dao)
    {
        $current_user = get_admin_user();

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $apply_refund = ApplyRefund::query()->with(['order', 'orderDetail', 'user', 'applyRefundReason'])->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::BUYER_SEND_SHIP->value)
                ->first();

            if (! $apply_refund) {
                throw new BusinessException('未找到待申请记录');
            }

            if ($apply_refund->transaction_id > 0) {
                throw new BusinessException('已执行退款，请勿重复操作');
            }

            // 操作退款
            $apply_refund_dao->operationRefund($apply_refund);

            DB::beginTransaction();

            try {
                $apply_refund_log_dao->addLog($apply_refund, $current_user->user_name, '卖家确认收货，已退款给买家', ApplyRefundLog::TYPE_SELLER);

                $order_log_dao->storeByAdminUser($current_user, $apply_refund->order, '卖家确认收货，已退款给买家');

                admin_operation_log("确认收货退款给买家，退款申请记录：{$apply_refund->id}");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('确认收货失败');
            }

            return $this->success('更新成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            Log::error('确认收货异常~'.$throwable);

            return $this->error('确认收货异常');
        }
    }

    /**
     * 查询快递.
     */
    public function queryExpress(Request $request, ExpressService $express_service)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $apply_refund = ApplyRefund::query()->whereId($id)->first();

            if (! $apply_refund) {
                throw new BusinessException('未找到待申请记录');
            }

            $shipping = $apply_refund->applyRefundShip;

            if (! $shipping) {
                throw new BusinessException('未找到发货信息');
            }

            $ship_list = $express_service->queryExpress($shipping->no, $shipping->shipCompany->code, $shipping->phone);

            $data['ship_list'] = $ship_list;
            $data['ship_no'] = $shipping->no;

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('查询快递信息异常');
        }
    }

    private function transformer(ApplyRefund $apply_refund): array
    {
        $user = $apply_refund->user;
        $order = $apply_refund->order;
        $orderDetail = $apply_refund->orderDetail;

        if (! $order || ! $orderDetail || ! $user) {
            return [];
        }

        return [
            'buyer_name' => $user->user_name,
            'order_sn' => $order->order_sn,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'goods_number' => $orderDetail->goods_number,
            'goods_name' => $orderDetail->goods_name,
            'goods_image' => $orderDetail->goods->image ?? '',
            'goods_price' => price_format($orderDetail->goods_price),
            'goods_amount' => price_format($orderDetail->goods_amount),
            'goods_integral' => $orderDetail->goods_integral,
            'goods_attr' => $orderDetail->skuValue(),
            'no' => $apply_refund->no,
            'type' => $apply_refund->type,
            'number' => get_new_price($apply_refund->number),
            'money' => $apply_refund->money,
            'integral' => $apply_refund->integral,
            'format_money' => price_format($apply_refund->money),
            'reason' => $apply_refund->applyRefundReason?->content,
            'description' => $apply_refund->description,
            'certificate' => $apply_refund->certificate,
            'result' => $apply_refund->result,
            'status' => $apply_refund->status,
            'isShipped' => (bool) $apply_refund->applyRefundShip,
            'time' => intval(strtotime($apply_refund->job_time)),
            'end_time' => $apply_refund->updated_at->format('Y-m-d H:i:s'),
            'server_time' => time(),
            'log' => $apply_refund->applyRefundLogs->map(function (ApplyRefundLog $item) use ($apply_refund, $user) {
                if ($item->type == ApplyRefundLog::TYPE_BUYER) {
                    $item->setAttribute('avatar', $user->avatar);
                    $item->setAttribute('user_name', $user->user_name);
                } else {
                    $item->setAttribute('avatar', '');
                    $item->setAttribute('user_name', $item->action_name);
                }
                $item->setAttribute('add_time', $item->created_at->format('Y-m-d H:i:s'));

                if ($item->apply_refund_data) {
                    $apply_refund_data = json_decode($item->apply_refund_data, true);
                    $item->setAttribute('money', price_format($apply_refund_data['money']));
                    $item->setAttribute('integral', $apply_refund_data['integral'] ?? 0);
                    $item->setAttribute('number', get_new_price($apply_refund_data['number']));
                    $item->setAttribute('reason', ApplyRefundReason::query()->whereId($apply_refund_data['reason_id'])->value('content') ?? '');
                    $item->setAttribute('description', $apply_refund_data['description'] ?? '');
                    $item->setAttribute('certificate', $apply_refund_data['certificate']);
                    $item->setAttribute('result', $apply_refund_data['result'] ?? '');
                } else {
                    $item->setAttribute('money', price_format($apply_refund->money));
                    $item->setAttribute('integral', $apply_refund->integral);
                    $item->setAttribute('number', get_new_price($apply_refund->number));
                    $item->setAttribute('reason', $apply_refund->applyRefundReason?->content);
                    $item->setAttribute('description', $apply_refund->description);
                    $item->setAttribute('certificate', $apply_refund->certificate);
                    $item->setAttribute('result', $apply_refund->result);
                }

                if ($item->apply_refund_ship_id > 0) {
                    $item->setAttribute('applyRefundShip', $apply_refund->applyRefundShip);
                    $item->setAttribute('applyRefundShip', $apply_refund->applyRefundShip);
                } else {
                    $item->setAttribute('applyRefundShip', null);
                }

                return $item->only('user_name', 'avatar', 'action', 'type', 'money', 'integral', 'number', 'reason', 'result', 'description', 'certificate', 'add_time', 'applyRefundShip');
            })->toArray(),
        ];
    }
}
