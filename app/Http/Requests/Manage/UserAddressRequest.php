<?php

namespace App\Http\Requests\Manage;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'user_id' => 'required|integer',
            'address_detail' => 'required|string|max:100|min:3',
            'consignee' => 'required|string',
            'phone' => 'required|integer',
            'province' => 'required|integer',
            'city' => 'required|integer',
            'district' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'id' => '地址ID',
            'user_id' => '用户id',
            'address_detail' => '详细地址',
            'consignee' => '收货人',
            'phone' => '手机号',
            'province' => '省份',
            'city' => '城市',
            'district' => '地区',
        ];
    }
}
