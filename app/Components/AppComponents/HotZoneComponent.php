<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class HotZoneComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => $this->getName() ?: '热区',
            'component_name' => $this->getComponentName(),
            'limit' => 0,
            'icon' => '&#xe7ce;',
            'sort' => '',
        ];
    }

    public function parameter(): array
    {
        return [
            'id' => '', // 组件自增id
            'name' => $this->getName() ?: '热区',
            'component_name' => $this->getComponentName(), // 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
            'sort' => Constant::ZERO,
            'content' => [
                'image' => '', // 图片地址
                'areas' => [
                    [
                        'x' => 89, // x坐标
                        'y' => 86, // y坐标
                        'width' => 750, // 宽度
                        'height' => 200, // 高度 200 ~ 2000
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'sort' => 1, // 排序
                    ],
                ],
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
            'id' => 'nullable|integer|exists:\App\Models\AppDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_HOT_ZONE,
            'is_show' => 'required|integer|in:'.Constant::ONE.','.Constant::ZERO,
            'content.image' => 'required', // 图片地址
            'content.areas' => 'required|array|max:10',
            'content.areas.*.x' => 'required|int',
            'content.items.*.y' => 'required|int',
            'content.items.*.width' => 'required|int',
            'content.items.*.height' => 'required|min:200|max:2000',
            'content.items.*.url.name' => 'present|nullable',
            'content.items.*.url.value' => 'present|nullable',
            'content.items.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
        ], $this->messages());
        if ($validator->fails()) {
            throw new ProcessDataException($this->getName().'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }
        $data = $validator->validated();
        $data['name'] = '热区';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_HOT_ZONE;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $items = collect($content['items'])->sortByDesc('sort')->map(function ($item) use (&$items) {
            if (!$item['is_show']) {
                return null;
            }

            return $item;
        })->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'image' => $content['image'],
            'items' => $content['areas'],
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
            'data' => $this->getContent($data), // 战术数据
        ];
    }

    private function messages(): array
    {
        return [
            // 基本字段验证
            'id.integer' => '板块ID 必须是整数',
            'id.exists' => '板块ID 不存在',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过100个字符',
            'component_name.required' => '组件名称不能为空',
            'component_name.in' => '组件名称无效，请选择正确的组件名称',
            'is_show.required' => '是否显示不能为空',
            'is_show.integer' => '是否显示必须是整数',
            'is_show.in' => '是否显示的值只能是 0 或 1',

            // content.image 验证
            'content.image.required' => '图片地址不能为空',

            // content.areas 验证
            'content.areas.required' => '区域数据不能为空',
            'content.areas.array' => '区域数据必须是一个数组',
            'content.areas.max' => '区域数据最多允许 10 个',

            // content.areas.* 验证
            'content.areas.*.x.required' => 'X 坐标不能为空',
            'content.areas.*.x.int' => 'X 坐标必须是整数',
            'content.areas.*.y.required' => 'Y 坐标不能为空',
            'content.areas.*.y.int' => 'Y 坐标必须是整数',
            'content.areas.*.width.required' => '宽度不能为空',
            'content.areas.*.width.int' => '宽度必须是整数',
            'content.areas.*.height.required' => '高度不能为空',
            'content.areas.*.height.min' => '高度最小值是 200',
            'content.areas.*.height.max' => '高度最大值是 2000',
            'content.areas.*.url.name.present' => '链接别名参数未设置',
            'content.areas.*.url.value.present' => '链接值参数未设置',
            'content.areas.*.sort.nullable' => '排序可以为空',
            'content.areas.*.sort.sometimes' => '排序字段有时需要验证',
            'content.areas.*.sort.integer' => '排序必须是整数',
            'content.areas.*.sort.min' => '排序最小值是 1',
            'content.areas.*.sort.max' => '排序最大值是 100',
        ];
    }
}
