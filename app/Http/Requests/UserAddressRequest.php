<?php

namespace App\Http\Requests;

use App\Exceptions\BusinessException;
use App\Models\UserAddress;
use App\Rules\PhoneRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'consignee' => 'required',
            'phone' => [
                'required',
                'integer',
                new PhoneRule,
            ],
            'province' => 'required|int',
            'city' => 'required|int',
            'district' => 'required|int',
            'address_detail' => 'required',
            'is_default' => 'in:'.UserAddress::DEFAULT.','.UserAddress::NOT_DEFAULT,
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'id',
            'consignee' => '收货人姓名',
            'phone' => '手机号',
            'province' => '省份',
            'city' => '城市',
            'district' => '区域',
            'address_detail' => '详细地址',
            'is_default' => '是否是默认地址',
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => 'ID 不存在',
            'consignee.required' => '请填写联系人信息',
            'phone.required' => '请填写联系人手机号信息',
            'phone.integer' => '手机号必须是整数',
            'phone.phone_rule' => '手机号格式不正确',
            'province.required' => '请填写省份信息',
            'province.int' => '省份信息格式不正确',
            'city.required' => '请填写城市信息',
            'city.int' => '城市信息格式不正确',
            'district.required' => '请填写地区信息',
            'district.int' => '地区信息格式不正确',
            'address_detail.required' => '请填写详细收货地址信息',
            'is_default.in' => '是否默认寄件地址参数错误',
        ];
    }
}
