<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchDestroyAddressRequest extends FormRequest
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
    public function rules()
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'integer',
                'exists:user_addresses,id',
            ],
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => '地址 ID 列表不能为空',
            'ids.array' => '地址 ID 列表必须是一个数组',
            'ids.*.integer' => '地址 ID 必须是整数',
            'ids.*.exists' => '地址 ID 不存在',
        ];
    }
}
