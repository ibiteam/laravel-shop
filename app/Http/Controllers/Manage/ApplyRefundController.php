<?php

namespace App\Http\Controllers\Manage;

use App\Enums\ApplyRefundStatusEnum;
use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Jobs\Order\ApplyRefundJob;
use App\Models\ApplyRefund;
use App\Models\ApplyRefundLog;
use App\Utils\KuaiDi100Util;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        $order_no = $request->get('order_no', '');
        $no = $request->get('no', '');
        $status = $request->get('status', null);
        $type = $request->get('type', null);
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $number = (int) $request->get('number', 10);

        $data = ApplyRefund::query()
            ->with(['user', 'order', 'orderDetail', 'applyRefundReason'])
            ->when($user_name, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('user_name', 'like', '%'.$user_name.'%')))
            ->when($goods_name, fn ($query) => $query->whereHas('orderDetail', fn ($query) => $query->where('goods_name', 'like', '%'.$goods_name.'%')))
            ->when($order_no, fn ($query) => $query->whereHas('order', fn ($query) => $query->where('no', 'like', '%'.$order_no.'%')))
            ->when($no, fn ($query) => $query->where('no', 'like', '%'.$no.'%'))
            ->when(! is_null($status), fn ($query) => $query->where('status', '=', $status))
            ->when(! is_null($type), fn ($query) => $query->where('type', '=', $type))
            ->when($start_time, fn ($query) => $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($end_time))))
            ->orderByDesc('created_at')->paginate($number);
        $data->getCollection()->transform(function (ApplyRefund $apply_refund) {
            return [
                'id' => $apply_refund->id,
                'no' => $apply_refund->no,
                'user_name' => $apply_refund->user?->user_name,
                'goods_name' => $apply_refund->orderDetail?->goods_name,
                'order_no' => $apply_refund->order?->no,
                'type' => strval($apply_refund->type),
                'status' => strval($apply_refund->status),
                'money' => $apply_refund->money,
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

        $applyRefund = ApplyRefund::query()
            ->with('applyRefundLogs', 'applyRefundLogs.applyRefundShip', 'applyRefundShip', 'applyRefundReason')
            ->with(['user' => function ($query) {
                return $query->select('id', 'user_name', 'avatar');
            }])
            ->with(['order' => function ($query) {
                return $query->select('id', 'no', 'user_id', 'created_at');
            }])
            ->with(['orderDetail' => function ($query) {
                return $query->select(['id', 'goods_id', 'goods_name', 'goods_number', 'goods_price', 'goods_amount', 'goods_unit', 'goods_sku_id', 'goods_sku_value'])
                    ->with(['goods' => function ($query) {
                        return $query->select(['id', 'image', 'unit']);
                    }]);
            }])
            ->whereId($id)->first();

        if (! $applyRefund) {
            return $this->error('未找到申请记录');
        }

        return $this->success($this->transformer($applyRefund));
    }

    /**
     * 同意申请.
     */
    public function agreeApply(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'money' => 'required|numeric',
            ], [], [
                'id' => '申请售后ID',
                'money' => '金额',
            ]);

            $id = $validated['id'];
            $money = (float) $validated['money'];

            $applyRefund = ApplyRefund::query()
                ->whereId($id)
                ->whereType(ApplyRefund::TYPE_REFUND_GOODS)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)
                ->first();

            if (! $applyRefund) {
                throw new BusinessException('申请记录不存在');
            }

            // todo
            // // 检查商家是否有收货地址
            // if (SellerShopInfoAddress::query()->whereIsUsed(SellerShopInfoAddress::USED)->doesntExist()) {
            //     return $this->error('请先添加用户退货的收货地址', 4001);
            // }

            // 支付成功记录
            // $payLog = OrderPayLog::whereOrderId($applyRefund->order_id)->wherePayStatus(OrderPayLog::PAY_STATUS_SUCCESS)->first();
            // if (! $payLog) {
            //     return $this->error('未找到支付成功记录');
            // }
            // if ($money > $payLog->money) {
            //     return $this->error('退款金额超过支付金额');
            // }

            // todo
            $buyer_refund_time = 5;

            DB::beginTransaction();

            try {
                $job_time = Carbon::now()->addDays($buyer_refund_time);
                $typeMsg = '退款退货';
                $applyRefund->money = $money;   // 修改退款金额
                $applyRefund->job_time = $job_time;
                $applyRefund->status = ApplyRefundStatusEnum::REFUSE_EXAMINE->value;
                $applyRefund->result = "卖家同意了本次{$typeMsg}申请";

                if (! $applyRefund->save()) {
                    throw new BusinessException('更新申请失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家同意了'.$typeMsg;
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('更新申请记录失败');
                }

                // 添加job 等待买家在 5 天内操作退货流程
                $action = '买家退货超时，退款流程系统自动关闭';
                ApplyRefundJob::dispatch(ApplyRefundStatusEnum::REFUSE_EXAMINE->value, $applyRefund->id, $action, ApplyRefundLog::TYPE_BUYER)->delay($job_time)->onQueue(config('cache.default_prefix'));
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('审核退款失败');
            }
            DB::commit();

            return $this->success('更新成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     *关闭申请.
     */
    public function closeApply(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $applyRefund = ApplyRefund::query()->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)
                ->first();

            if (! $applyRefund) {
                throw new BusinessException('申请记录不存在');
            }

            $applyRefund->status = ApplyRefundStatusEnum::REFUND_CLOSE->value;
            $applyRefund->result = '退款流程已关闭，交易正常进行';

            if (! $applyRefund->save()) {
                throw new BusinessException('关闭申请失败');
            }
            $applyRefundLog = new ApplyRefundLog;
            $applyRefundLog->apply_refund_id = $applyRefund->id;
            $applyRefundLog->action = '卖家已发货，退款流程关闭，交易正常进行';
            $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

            if (! $applyRefundLog->save()) {
                throw new BusinessException('关闭申请记录失败');
            }

            return $this->success('关闭成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 执行退款.
     */
    public function executeRefund(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'money' => 'required|numeric',
            ], [], [
                'id' => '申请售后ID',
                'money' => '金额',
            ]);

            $id = $validated['id'];
            $money = (float) $validated['money'];

            $applyRefund = ApplyRefund::query()->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::NOT_PROCESSED->value)->first();

            if (! $applyRefund) {
                throw new BusinessException('申请记录不存在');
            }

            // 修改退款金额
            if ($money) {
                $applyRefund->money = $money;
            }

            // 微信退款
            $this->wechatRefund($applyRefund);

            DB::beginTransaction();

            try {
                $applyRefund->type = ApplyRefund::TYPE_REFUND_MONEY;
                $applyRefund->status = ApplyRefundStatusEnum::REFUND_SUCCESS->value;
                $applyRefund->result = '卖家同意了退款';

                if (! $applyRefund->save()) {
                    throw new BusinessException('更新申请失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家同意了退款';
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('更新申请记录失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家主动同意退款给买家';
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('更新申请记录失败');
                }

                // 更新订单退款后的状态
                $order_info = app(ApplyRefundDao::class)->changeOrderStatus($applyRefund);

                // 记录操作日志
                // if (! app(OrderActionDao::class)->log($order_info, get_seller_user()->user_name ?? OrderAction::ACTION_SELLER, '卖家主动同意退款给买家')) {
                //     throw new BusinessException('订单退款成功，买家申请退款:订单记录日志失败');
                // }
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('执行退款失败');
            }
            DB::commit();

            return $this->success('更新成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 拒绝退款.
     */
    public function refuseRefund(Request $request)
    {
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

            $applyRefund = ApplyRefund::query()->whereId($id)->first();

            if (! $applyRefund) {
                throw new BusinessException('申请记录不存在');
            }

            DB::beginTransaction();

            try {
                // todo
                $buyer_change_time = 72;
                $job_time = Carbon::now()->addHours($buyer_change_time);
                $applyRefund->status = ApplyRefundStatusEnum::REFUSE->value;
                $applyRefund->result = $result;
                $applyRefund->job_time = Carbon::now()->addHours($buyer_change_time);

                if (! $applyRefund->save()) {
                    throw new BusinessException('拒绝退款失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家拒绝了退货退款';
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('拒绝退款记录失败');
                }

                // 添加job 等待买家再72小时内操作关闭了流程
                $action = '买家超时未申请，退款流程自动关闭';
                ApplyRefundJob::dispatch(ApplyRefundStatusEnum::REFUSE->value, $applyRefund->id, $action, ApplyRefundLog::TYPE_SELLER)->delay($job_time)->onQueue(config('cache.default_prefix'));
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('拒绝退款失败');
            }
            DB::commit();

            return $this->success('拒绝退款成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 确认收货.
     */
    public function confirmReceipt(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $applyRefund = ApplyRefund::query()->whereId($id)
                ->whereStatus(ApplyRefundStatusEnum::BUYER_SEND_SHIP->value)->first();

            if (! $applyRefund) {
                throw new BusinessException('未找到待申请记录');
            }

            // 微信退款
            $this->wechatRefund($applyRefund);

            DB::beginTransaction();

            try {
                $applyRefund->status = ApplyRefundStatusEnum::REFUND_SUCCESS->value;
                $applyRefund->job_time = 0;
                $applyRefund->result = '款项已原路返回买家账号';

                if (! $applyRefund->save()) {
                    throw new BusinessException('确认收货失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家确认收货';
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('确认收货记录失败');
                }

                $applyRefundLog = new ApplyRefundLog;
                $applyRefundLog->apply_refund_id = $applyRefund->id;
                $applyRefundLog->action = '卖家确认收货，已退款给买家';
                $applyRefundLog->type = ApplyRefundLog::TYPE_SELLER;

                if (! $applyRefundLog->save()) {
                    throw new BusinessException('确认收货记录失败');
                }

                // 更新订单退款后的状态
                app(ApplyRefundDao::class)->changeOrderStatus($applyRefund);
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('确认收货失败');
            }
            DB::commit();

            return $this->success('更新成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 查询快递 物流轨迹.
     */
    public function queryExpress(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '申请售后ID',
            ]);

            $id = $validated['id'];

            $applyRefund = ApplyRefund::query()->whereId($id)->first();

            if (! $applyRefund) {
                throw new BusinessException('未找到待申请记录');
            }
            $shipping = $applyRefund->applyRefundShip;

            if (! $shipping) {
                throw new BusinessException('未找到发货信息');
            }

            $data = KuaiDi100Util::queryExpress($shipping->no, $shipping->shipCompany->code, $shipping->phone);

            return $this->success($data);

        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('查询快递物流信息异常');
        }
    }

    private function transformer(ApplyRefund $applyRefund): array
    {
        $user = $applyRefund->user;
        $temp_unit = $applyRefund->orderDetail->goods_unit ?? ($applyRefund->orderDetail->goods->unit ?? '');

        return [
            'server_time' => time(),
            'goods_thumb' => $applyRefund->orderDetail->goods->image ?? '',
            'buyer_name' => $user->user_name ?? '',
            'order_no' => $applyRefund->order->no ?? '',
            'add_time' => $applyRefund->order->created_at,
            'goods_number' => $applyRefund->orderDetail->goods_number,
            'goods_name' => $applyRefund->orderDetail->goods_name,
            'goods_sku_value' => $applyRefund->orderDetail->goods_sku_value,
            'goods_price' => price_format($applyRefund->orderDetail->goods_price),
            'goods_amount' => price_format($applyRefund->orderDetail->goods_amount),
            'refund_number' => get_new_price($applyRefund->number),
            'unit' => $temp_unit,
            'refund_no' => $applyRefund->no,
            'type' => $applyRefund->type,
            'certificate' => $applyRefund->certificate,
            'money' => $applyRefund->money,
            'format_money' => price_format($applyRefund->money),
            'reason' => $applyRefund->applyRefundReason->content,
            'result' => $applyRefund->result,
            'description' => $applyRefund->description,
            'status' => $applyRefund->status,
            'time' => strtotime($applyRefund->job_time),
            'end_time' => $applyRefund->updated_at->toDateTimeString(),
            'isShipped' => (bool) $applyRefund->applyRefundShip,
            // 'shopAddress' => $shopAddress ? $this->formatAddress($shopAddress) : null,
            'log' => $applyRefund->applyRefundLogs->map(function (ApplyRefundLog $item) use (&$user, $temp_unit) {
                if ($item->type === ApplyRefundLog::TYPE_BUYER) {
                    $item->setAttribute('user_name', $user->user_name);
                    $item->setAttribute('avatar', $user->avatar);
                } else {
                    $item->setAttribute('user_name', '');
                    $item->setAttribute('avatar', '');
                }
                $item->setAttribute('unit', $temp_unit);
                $item->setAttribute('add_time', $item->created_at->toDateTimeString());
                $item->setAttribute('money', price_format($item->applyRefund->money));
                $item->setAttribute('refund_number', get_new_price($item->applyRefund->number));

                return $item->only('user_name', 'avatar', 'action', 'type', 'money', 'refund_number', 'unit', 'add_time', 'applyRefund', 'applyRefundShip');
            })->toArray(),
        ];
    }

    /**
     * 微信退款.
     */
    private function wechatRefund(ApplyRefund $applyRefund)
    {
        // todo

        // try {
        //     $payLog = OrderPayLog::whereOrderId($applyRefund->order_id)->wherePayStatus(OrderPayLog::PAY_STATUS_SUCCESS)->first();
        //
        //     if (! $payLog) {
        //         throw new BusinessException('未找到支付成功记录');
        //     }
        //
        //     if ($applyRefund->money > $payLog->money) {
        //         throw new BusinessException('退款金额超过支付金额');
        //     }
        //
        //     if ($sub_mch_id = $applyRefund->shop->sub_mch_id ?? '') {
        //         $wechat_res = WechatServicePaymentService::refundOrderMany($sub_mch_id, $payLog->pay_sn, $applyRefund->flow_sn, $payLog->money, $applyRefund->money);
        //     } else {
        //         $wechat_res = WechatPaymentService::refundOrderMany($payLog->pay_sn, $applyRefund->flow_sn, $payLog->money, $applyRefund->money);
        //     }
        //
        //     if (isset($wechat_res['return_code']) && $wechat_res['return_code'] === 'SUCCESS' && isset($wechat_res['result_code']) && $wechat_res['result_code'] === 'SUCCESS') {
        //         $payLog->refund_money += $applyRefund->money;
        //
        //         if ($payLog->refund_money >= $payLog->money) {
        //             $payLog->pay_status = OrderPayLog::PAY_STATUS_RETURN;
        //         }
        //         $payLog->save();
        //     } else {
        //         throw new BusinessException($wechat_res['err_code_des'] ?? '请求微信退款错误');
        //     }
        // } catch (\Exception $exception) {
        //     throw new BusinessException('退款失败');
        // }
    }
}
