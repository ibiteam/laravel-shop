<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AppDecorationItem;
use App\Models\Goods;
use App\Models\ShopConfig;
use App\Utils\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecommendComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => '为您推荐',
            'component_name' => $this->getComponentName(),
            'limit' => 1,
            'icon' => '&#xe7d7',
            'sort' => '',
        ];
    }

    public function parameter(): array
    {
        return [
            'id' => '', // 组件自增id
            'name' => $this->getName() ?: '为您推荐',
            'component_name' => $this->getComponentName(), // 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
            'sort' => Constant::ZERO,
            'content' => [
                'title' => '', // 标题
            ],
        ];
    }

    /**
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     *
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_RECOMMEND,
            'is_show' => 'required|integer|in:'.Constant::ONE.','.Constant::ZERO,
            'content.title' => 'required', // 标题
        ], $this->messages());

        if ($validator->fails()) {
            throw new ProcessDataException($this->getName().'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }
        $data = $validator->validated();
        $data['name'] = '为您推荐';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_RECOMMEND;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        // 是否展示销量
        $is_show_sales_volume = shop_config(ShopConfig::IS_SHOW_SALES_VOLUME);

        $items = Goods::query()
            ->select('no', 'image', 'name', 'price', 'label')
            ->addSelect(DB::raw("CASE WHEN {$is_show_sales_volume} THEN sales_volume ELSE NULL END AS sales_volume"))
            ->orderByDesc('sales_volume')
            ->orderByDesc('id')
            ->paginate(6);

        $datas = new CommonResourceCollection($items);

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'title' => $content['title'],
            'items' => $datas,
        ];
    }

    /**
     * 后台数据渲染.
     */
    public function display($data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'sort' => $data['sort'],
            'content' => $data['content'] ?? null, // 表单提交数据
            'data' => $this->getContent($data), //
        ];
    }

    private function messages(): array
    {
        return [
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID 不正确',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过 100 个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => '板块数据格式不正确',
            'content.title.required' => '标题 参数未设置',
        ];
    }
}
