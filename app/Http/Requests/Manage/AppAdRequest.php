<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class AppAdRequest extends FormRequest
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
            'id' => 'required|integer',
            'name' => 'nullable|string|max:255',
            'image' => 'required|string',
            'sort' => 'required|integer',
            'link_type' => 'required|integer|in:1,2',
            'link' => 'nullable|string',
            'is_show' => 'required|integer|in:0,1',
            'type' => 'required|integer|in:1,2',
            'start_time' => 'required_if:type,1|nullable|date_format:Y-m-d H:i:s',
            'end_time' => 'required_if:type,1|nullable|date_format:Y-m-d H:i:s|after_or_equal:start_time',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'ID不能为空',
            'id.integer' => 'ID数据类型有误',
            'name.string' => '标题必须是字符串',
            'name.max' => '标题最多不能超过 255 个字符',
            'image.required' => '广告图是必填项',
            'image.string' => '广告图必须是字符串',
            'sort.required' => '排序是必填项',
            'sort.integer' => '排序必须是整数',
            'link_type.required' => '链接类型是必填项',
            'link_type.integer' => '链接类型必须是整数',
            'link_type.in' => '链接类型必须为 1（https）或 2（移动端链接）',
            'link.string' => '链接必须是字符串',
            'is_show.required' => '是否展示是必填项',
            'is_show.integer' => '是否展示必须是整数',
            'is_show.in' => '是否展示必须为 0（否）或 1（是）',
            'type.required' => '广告类型是必填项',
            'type.integer' => '广告类型必须是整数',
            'type.in' => '广告类型必须为 1（时间限制）或 2（长久广告）',
            'start_time.date_format' => '开始时间格式必须为 Y-m-d H:i:s',
            'end_time.date_format' => '结束时间格式必须为 Y-m-d H:i:s',
            'end_time.after_or_equal' => '结束时间必须大于或等于开始时间',
        ];
    }
}
