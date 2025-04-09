<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class LabelComponent extends PageComponent
{
    /**
     * 是否是固定组件 1是 0否
     */
    private int $fixed_assembly_yes = 1;


    public function icon(): array
    {
        return [];
    }

    public function parameter(): array
    {
        return [
            'id' => '',// 组件自增id
            'name' => '底部标签栏',
            'component_name' => AppDecorationItem::COMPONENT_NAME_LABEL,
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_yes, // 是否是固定组件 1是 0否
            'content' => [
                'font_default_color' => '#333', // 默认选中颜色
                'font_selection_color' => '#f71111', // 标签选中字体颜色
                'data' => [
                    [
                        'url' => [
                            'name' => '', // 路由名称
                            'value' => '', // 路由链接
                        ],
                        'default_title' => '', // 标签默认名称
                        'selection_title' => '', // 标签选中名称
                        'default_image' => '', // 标签默认图标
                        'selection_image' => '', // 标签选中图标
                        'is_show' => Constant::ONE, // 是否显示 1、显示 0、不显示
                    ]
                ]
            ],
        ];
    }


    /**
     * @param $data
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        $is_show_validate_string = Constant::ONE . ',' . Constant::ZERO;
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:' . AppDecorationItem::COMPONENT_NAME_LABEL,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.font_default_color' => 'required|string',
            'content.font_selection_color' => 'required|string',
            'content.data.*.default_title' => 'present|nullable',
            'content.data.*.selection_title' => 'present|nullable',
            'content.data.*.default_image' => 'present|nullable',
            'content.data.*.selection_image' => 'present|nullable',
            'content.data.*.url.name' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
        ], $this->messages());
        if ($validator->fails()) {
            throw new ProcessDataException('底部标签栏组件'.'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data['name'] = $validator['name'];
        $data['component_name'] = $validator['component_name'];
        $data['is_fixed_assembly'] = $this->fixed_assembly_yes;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $items = collect($content['data'])->map(function ($item) use (&$items) {
            if (!$item['is_show']) {
                return null;
            }

            return $item;
        })->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'font_default_color' => $content['font_default_color'] ?? '',
            'font_selection_color' => $content['font_selection_color'] ?? '',
            'items' => $items,
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
            'content.font_default_color.required' => '默认选中颜色 参数未设置',
            'content.font_default_color.string' => '默认选中颜色 参数格式不正确',
            'content.font_selection_color.required' => '标签选中字体颜色 参数未设置',
            'content.font_selection_color.string' => '标签选中字体颜色 参数格式不正确',
            'content.data.array' => '内容数据格式不正确',
            'content.data.*.default_title.present' => '标签默认名称 参数未设置',
            'content.data.*.selection_title.present' => '标签选中名称 参数未设置',
            'content.data.*.default_image.present' => '标签默认图标 参数未设置',
            'content.data.*.selection_image.present' => '标签选中图标 参数未设置',
            'content.data.*.url.name.present' => '路由名称 参数未设置',
            'content.data.*.url.value.present' => '路由链接 参数未设置',
        ];
    }
}
