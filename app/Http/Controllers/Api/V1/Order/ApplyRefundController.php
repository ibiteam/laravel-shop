<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Requests\ApplyRefundStoreRequest;
use App\Models\ApplyRefund;
use App\Services\Order\ApplyRefundService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplyRefundController extends BaseController
{
    // 检测是否允许申请售后
    public function verify(Request $request, ApplyRefundDao $apply_refund_dao)
    {
        try {
            $validated = $request->validate([
                'order_no' => 'required|string',
                'order_detail_id' => 'required|integer',
            ], [], [
                'order_no' => '订单编号',
                'order_detail_id' => '订单明细ID',
            ]);

            $apply_refund_dao->verifyApply($this->user(), $validated['order_no'], $validated['order_detail_id']);

            return $this->success('允许申请售后');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('申请售后检测异常~');
        }
    }

    /**
     * 获取申请售后类型与售后商品信息.
     */
    public function init(Request $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $validated = $request->validate([
                'order_no' => 'required|string',
                'order_detail_id' => 'required|integer',
            ], [], [
                'order_no' => '订单编号',
                'order_detail_id' => '订单明细ID',
            ]);

            $data = $apply_refund_service->init($this->user(), $validated['order_no'], $validated['order_detail_id']);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('申请售后初始化异常~');
        }
    }

    /**
     * 根据售后类型，获取对应的数据.
     */
    public function getInfoByType(Request $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $validated = $request->validate([
                'order_no' => 'required|string',
                'order_detail_id' => 'required|integer',
                'type' => 'required|integer|in:'.implode(',', [ApplyRefund::TYPE_REFUND_MONEY, ApplyRefund::TYPE_REFUND_GOODS]),
            ], [], [
                'order_no' => '订单编号',
                'order_detail_id' => '订单明细ID',
                'type' => '售后类型',
            ]);

            $data = $apply_refund_service->getInfoByTypeAndOrder($this->user(), $validated['order_no'], $validated['order_detail_id'], $validated['type']);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('申请售后获取数据异常~');
        }
    }

    /**
     * 申请售后 发起.
     */
    public function store(ApplyRefundStoreRequest $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $params = $request->only(['apply_refund_id', 'type', 'order_no', 'order_detail_id', 'number', 'money', 'reason_id', 'description', 'certificate']);

            $apply_refund = $apply_refund_service->launchRefund($this->user(), $params);

            return $this->success(['apply_refund_id' => $apply_refund->id]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('申请售后发起异常~');
        }
    }

    /**
     * 撤销申请.
     */
    public function revoke(Request $request, ApplyRefundDao $apply_refund_dao)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required|integer',
            ], [], [
                'apply_refund_id' => '申请售后ID',
            ]);

            $apply_refund_dao->revoke($this->user(), $validated['apply_refund_id']);

            return $this->success('撤销成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('撤销申请异常~');
        }
    }
}
