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
            'goods_sn' => 'nullable|string',
            'category_id' => 'required|integer',
            'seller_category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'goods_name' => 'required|string|max:191',
            'goods_sub_name' => 'required|string|max:191',
            'keywords' => 'required|string|max:191',
            'buy_min_number' => 'required|integer|min:1',
            'images' => 'required|array',
            'images.*.url' => 'required|string|url',
            'images.*.type' => 'required|integer|boolean',
            'video' => 'nullable|string|url',
            'video_duration' => 'required_with:video|integer|min:1',
            'content' => 'required|string',
            'parameters' => 'required|array',
            'parameters.*.name' => 'required|string|max:191',
            'parameters.*.value' => 'required|string|max:191',
            'price' => 'required|numeric|min:0.01',
            'number' => 'required|integer|min:1',
            'goods_specs' => 'required_with:goods_skus|array',
            'goods_specs.*.id' => 'required|integer',
            'goods_specs.*.name' => 'required|string|max:191',
            'goods_specs.*.values' => 'required|array',
            'goods_specs.*.values.*.id' => 'required|integer',
            'goods_specs.*.values.*.name' => 'required|string',
            'goods_skus' => 'required_with:goods_specs|array',
            'goods_skus.*.id' => 'required|integer',
            'goods_skus.*.thumb' => 'required|string|url',
            'goods_skus.*.price' => 'required|numeric|min:0.01',
            'goods_skus.*.number' => 'required|integer|min:1',
            'goods_skus.*.is_show' => 'required|boolean',
            'goods_skus.*.template_1' => 'required|string',
            'goods_skus.*.template_2' => 'nullable|string',
            'goods_skus.*.template_3' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => '分类',
            'seller_category_id' => '商家分类',
            'brand_id' => '品牌ID',
            'goods_name' => '商品标题',
            'goods_sub_name' => '商品副标题',
            'keywords' => '商品关键词',
            'buy_min_number' => '最小起订量',
            'images' => '商品图片',
            'images.*.url' => '商品图片地址',
            'images.*.type' => '商品图片类型',
            'video' => '商品视频',
            'video_duration' => '视频时长',
            'content' => '详细信息',
            'parameters' => '商品参数',
            'parameters.*.name' => '参数名称',
            'parameters.*.value' => '参数值',
            'price' => '商品价格',
            'number' => '商品库存',
            'goods_specs' => '商品规格',
            'goods_specs.*.id' => '规格ID',
            'goods_specs.*.name' => '规格名称',
            'goods_specs.*.values' => '规格值',
            'goods_specs.*.values.*.id' => '规格值ID',
            'goods_specs.*.values.*.name' => '规格值名称',
            'goods_skus' => '商品SKU',
            'goods_skus.*.id' => 'SKU ID',
            'goods_skus.*.goods_id' => '商品ID',
            'goods_skus.*.thumb' => '商品SKU缩略图',
            'goods_skus.*.price' => '商品SKU价格',
            'goods_skus.*.number' => '商品SKU库存',
            'goods_skus.*.is_show' => '商品SKU是否显示',
            'goods_skus.*.template_1' => '商品SKU模板1',
            'goods_skus.*.template_2' => '商品SKU模板2',
            'goods_skus.*.template_3' => '商品SKU模板3',
        ];
    }
}
