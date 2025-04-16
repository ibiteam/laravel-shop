<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\ApplyRefundDao;
use App\Http\Dao\ApplyRefundLogDao;
use App\Http\Dao\ApplyRefundShipDao;
use App\Http\Requests\ApplyRefundStoreRequest;
use App\Http\Resources\Api\ApplyRefundResourceCollection;
use App\Models\ApplyRefund;
use App\Models\ShipCompany;
use App\Services\ApplyRefundService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplyRefundController extends BaseController
{
    /**
     * 检测是否允许申请售后.
     */
    public function verify(Request $request, ApplyRefundDao $apply_refund_dao)
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
                'order_detail_id' => 'required|integer',
            ], [], [
                'order_sn' => '订单编号',
                'order_detail_id' => '订单明细ID',
            ]);

            $apply_refund_dao->verifyApply($this->user(), $validated['order_sn'], $validated['order_detail_id']);

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
                'order_sn' => 'required|string',
                'order_detail_id' => 'required|integer',
            ], [], [
                'order_sn' => '订单编号',
                'order_detail_id' => '订单明细ID',
            ]);

            $data = $apply_refund_service->init($this->user(), $validated['order_sn'], $validated['order_detail_id']);

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
    public function show(Request $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
                'order_detail_id' => 'required|integer',
                'type' => 'required|integer|in:'.implode(',', [ApplyRefund::TYPE_REFUND_MONEY, ApplyRefund::TYPE_REFUND_GOODS]),
            ], [], [
                'order_sn' => '订单编号',
                'order_detail_id' => '订单明细ID',
                'type' => '售后类型',
            ]);

            $data = $apply_refund_service->getInfoByTypeAndOrder($this->user(), $validated['order_sn'], $validated['order_detail_id'], $validated['type']);

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
     * 申请售后 新增编辑.
     */
    public function store(ApplyRefundStoreRequest $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $params = $request->only(['apply_refund_id', 'type', 'order_sn', 'order_detail_id', 'number', 'money', 'reason_id', 'description', 'certificate']);

            $apply_refund = $apply_refund_service->launchRefund($this->user(), $params);

            return $this->success(['apply_refund_id' => $apply_refund->id]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('申请售后发起异常~'.$throwable->getMessage());
        }
    }

    /**
     * 退款售后 列表.
     */
    public function list(Request $request, ApplyRefundDao $apply_refund_dao)
    {
        try {
            $validated = $request->validate([
                'keywords' => 'nullable|string',
                'page' => 'required|integer|min:1',
                'number' => 'required|integer|min:1',
            ], [], [
                'keywords' => '关键词',
                'page' => '页码',
                'number' => '每页数量',
            ]);

            $keywords = $validated['keywords'] ?? '';

            $list = $apply_refund_dao->getListByUser($this->user(), $keywords, $validated['page'], $validated['number']);

            return $this->success(new ApplyRefundResourceCollection($list));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取退款售后列表异常~');
        }
    }

    /**
     * 售后详情.
     */
    public function detail(Request $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required_without:order_sn,order_detail_id|integer',
                'order_sn' => 'required_without:apply_refund_id|string',
                'order_detail_id' => 'required_without:apply_refund_id|integer',
            ], [], [
                'apply_refund_id.required_without' => '申请售后ID参数错误',
                'apply_refund_id.integer' => '申请售后ID格式不正确',
                'order_sn.required_without' => '订单编号参数错误',
                'order_sn.string' => '订单编号格式不正确',
                'order_detail_id.required_without' => '订单明细ID参数错误',
                'order_detail_id.integer' => '订单明细ID格式不正确',
            ]);

            $order_sn = $validated['order_sn'] ?? '';
            $order_detail_id = $validated['order_detail_id'] ?? 0;
            $apply_refund_id = $validated['apply_refund_id'] ?? 0;

            $data = $apply_refund_service->getDetailByOrderOrId($this->user(), $apply_refund_id, $order_sn, $order_detail_id, false);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取售后详情异常~');
        }
    }

    /**
     * 撤销申请.
     */
    public function revoke(Request $request, ApplyRefundService $apply_refund_service)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required|integer',
            ], [], [
                'apply_refund_id' => '申请售后ID',
            ]);

            $apply_refund_service->revoke($this->user(), $validated['apply_refund_id']);

            return $this->success('撤销成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('撤销申请异常~');
        }
    }

    /**
     * 协商历史.
     */
    public function log(Request $request, ApplyRefundLogDao $apply_refund_log_dao)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required|integer',
            ], [], [
                'apply_refund_id' => '申请售后ID',
            ]);

            $data = $apply_refund_log_dao->logList($this->user(), $validated['apply_refund_id']);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取协商历史异常~');
        }
    }

    /**
     * 退货物流信息.
     */
    public function shipInfo(Request $request, ApplyRefundShipDao $apply_refund_ship_dao)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required|integer',
            ], [], [
                'apply_refund_id' => '申请售后ID',
            ]);

            $apply_refund = ApplyRefund::query()->with(['order', 'applyRefundShip'])->whereId($validated['apply_refund_id'])->whereUserId($this->user()->id)->first();

            if (! $apply_refund instanceof ApplyRefund) {
                throw new BusinessException('退款信息不存在');
            }

            $ship_companies = ShipCompany::query()->whereStatus(ShipCompany::STATUS_ENABLE)->select(['id', 'name'])->get();

            $data = [
                'mobile' => $apply_refund->order->phone,
                'ship_companies' => $ship_companies,
            ];

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('用户填写退货物流异常~');
        }
    }

    /**
     * 用户填写退货物流.
     */
    public function shipAdd(Request $request, ApplyRefundShipDao $apply_refund_ship_dao)
    {
        try {
            $validated = $request->validate([
                'apply_refund_id' => 'required|integer',
                'no' => 'required|string',
                'ship_company_id' => 'required|integer',
                'phone' => 'required|is_phone',
                'description' => 'nullable|string',
                'certificate' => 'nullable|string',
            ], [], [
                'apply_refund_id' => '申请售后ID',
                'no' => '物流单号',
                'ship_company_id' => '物流公司ID',
                'phone' => '手机号',
                'description' => '描述',
                'certificate' => '凭证',
            ]);

            $apply_refund_ship_dao->addShip($this->user(), $validated['apply_refund_id'], $validated['no'], $validated['ship_company_id'], $validated['phone'], $validated['description'] ?? null, $validated['certificate'] ?? null);

            return $this->success('填写成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('用户填写退货物流异常~');
        }
    }
}
