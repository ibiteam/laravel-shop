<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class GoodsStoreRequest extends FormRequest
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
            'category_id' => 'required|integer',
            'name' => 'required|string|max:60',
            'label' => 'nullable|string|max:6',
            'sub_name' => 'nullable|string|max:60',
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'required|string|max:191',
            'parameters.*.value' => 'required|string|max:191',
            'images' => 'required|array',
            'images.*' => 'required|string|url',
            'video' => 'nullable|string|url',
            'video_duration' => 'required_with:video|integer',
            'content' => 'required|string',
            'unit' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'integral' => 'required|numeric|min:0',
            'total' => 'required|integer',
            'type' => 'required|integer|in:0,1',
            'status' => 'required|integer|in:0,1',
            'can_quota' => 'required|integer|in:0,1',
            'quota_number' => 'required|integer|min:0',
            'spec_data' => 'required_with:sku_data|array',
            'spec_data.*.id' => 'nullable|integer',
            'spec_data.*.name' => 'required|string|max:191',
            'spec_data.*.values' => 'required|array',
            'spec_data.*.values.*.id' => 'nullable|integer',
            'spec_data.*.values.*.name' => 'required|string',
            'sku_data' => 'required_with:spec_data|array',
            'sku_data.*.id' => 'nullable|integer',
            'sku_data.*.thumb' => 'required|string|url',
            'sku_data.*.price' => 'required|numeric|min:0.01',
            'sku_data.*.integral' => 'required|numeric|min:0',
            'sku_data.*.number' => 'required|integer|min:1',
            'sku_data.*.is_show' => 'required|boolean',
            'sku_data.*.template_1' => 'required|string',
            'sku_data.*.template_2' => 'nullable|string',
            'sku_data.*.template_3' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => '分类',
            'name' => '商品名称',
            'label' => '商品标签',
            'sub_name' => '商品副标题',
            'parameters' => '商品参数',
            'parameters.*.name' => '商品参数名',
            'parameters.*.value' => '商品参数值',
            'images' => '商品图片',
            'images.*' => '商品图片',
            'video' => '商品视频',
            'video_duration' => '商品视频时长',
            'content' => '商品详情',
            'unit' => '商品单位',
            'price' => '商品价格',
            'integral' => '积分',
            'total' => '商品库存',
            'type' => '商品类型',
            'status' => '商品状态',
            'can_quota' => '商品是否限购',
            'quota_number' => '商品限购数量',
            'spec_data' => '商品规格',
            'spec_data.*.id' => '商品规格ID',
            'spec_data.*.name' => '商品规格名称',
            'spec_data.*.values' => '商品规格值',
            'spec_data.*.values.*.id' => '商品规格值ID',
            'spec_data.*.values.*.name' => '商品规格值名称',
            'sku_data' => '商品SKU',
            'sku_data.*.id' => '商品SKU ID',
            'sku_data.*.thumb' => '商品SKU缩略图',
            'sku_data.*.price' => '商品SKU价格',
            'sku_data.*.integral' => '商品SKU积分',
            'sku_data.*.number' => '商品SKU库存',
            'sku_data.*.is_show' => '商品SKU是否显示',
            'sku_data.*.template_1' => '商品SKU模板1',
            'sku_data.*.template_2' => '商品SKU模板2',
            'sku_data.*.template_3' => '商品SKU模板3',
        ];
    }
}
