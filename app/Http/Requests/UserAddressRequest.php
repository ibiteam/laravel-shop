<?php

namespace App\Http\Requests;

use App\Exceptions\BusinessException;
use App\Models\UserAddress;
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
            'phone' => 'required',
            'province' => 'required|int',
            'city' => 'required|int',
            'district' => 'required|int',
            'address_detail' => 'required',
            'is_default' => 'in:'.UserAddress::DEFAULT.','.UserAddress::NOT_DEFAULT,
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => 'ID 不存在',
            'consignee.required' => '请填写联系人信息',
            'phone.required' => '请填写联系人手机号信息',
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
