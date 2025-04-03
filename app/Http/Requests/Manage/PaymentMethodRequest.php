<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'is_enabled' => 'required|boolean',
            'icon' => 'required|string|url',
            'description' => 'nullable|string|max:255',
            'config' => 'required|array',
            'limit' => 'required|integer',
            'is_recommend' => 'required|boolean',
            'sort' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => '支付方式ID',
            'name' => '名称',
            'is_enabled' => '是否启用',
            'icon' => '图标',
            'description' => '描述',
            'config' => '配置',
            'limit' => '限额',
            'is_recommend' => '是否推荐',
            'sort' => '排序',
        ];
    }
}
