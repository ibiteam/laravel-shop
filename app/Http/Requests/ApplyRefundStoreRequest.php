<?php

namespace App\Http\Requests;

use App\Exceptions\BusinessException;
use App\Models\ApplyRefund;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ApplyRefundStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'apply_refund_id' => 'required_without_all:order_sn,order_detail_id|integer',
            'order_sn' => 'required_without:apply_refund_id|string',
            'order_detail_id' => 'required_without:apply_refund_id|integer',
            'number' => 'required|numeric',
            'money' => 'required|numeric',
            'integral' => 'required|numeric',
            'type' => 'required|integer|in:'.implode(',', [ApplyRefund::TYPE_REFUND_GOODS, ApplyRefund::TYPE_REFUND_MONEY]),
            'reason_id' => 'required|integer',
            'description' => 'nullable|string',
            'certificate' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'apply_refund_id.required_without_all' => '申请售后参数错误',
            'apply_refund_id.integer' => '申请售后参数格式错误',
            'order_sn.required_without' => '订单参数有误',
            'order_sn.string' => '订单参数有误',
            'order_detail_id.required_without' => '订单明细参数有误',
            'order_detail_id.integer' => '订单明细参数有误',
            'number.required' => '申请售后数量不能为空',
            'number.numeric' => '申请售后数量格式不正确',
            'money.required' => '退款金额不能为空',
            'money.numeric' => '退款金额格式不正确',
            'integral.required' => '退款积分不能为空',
            'integral.numeric' => '退款积分格式不正确',
            'reason_id.required' => '退款原因不能为空',
            'reason_id.integer' => '退款原因格式不正确',
            'description.string' => '退款描述格式不正确',
            'certificate.string' => '商品凭证格式不正确',
            'type.sometimes' => '售后类型不正确',
            'type.required' => '请设置售后类型',
            'type.integer' => '售后类型格式不正确',
            'type.in' => '售后类型格式不正确！',
        ];
    }

    /**
     * @throws BusinessException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new BusinessException($validator->errors()->first());
    }
}
