<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class HomeNavComponent extends PageComponent
{
    /**
     * 是否是固定组件 1是 0否.
     */
    private int $fixed_assembly_yes = 1;

    public function icon(): array
    {
        return [];
    }

    public function parameter(): array
    {
        return [
            'id' => '', // 组件自增id
            'name' => '搜索',
            'component_name' => AppDecorationItem::COMPONENT_NAME_HOME_NAV,
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_yes, // 是否是固定组件 1是 0否
            'content' => [
                'logo' => '', // logo
                'keywords' => '', // 关键词
                'button_color' => '#f71111', // 按钮背景色
                'interval' => 3, // 关键词轮播时间，最大10 最小1
                'data' => [
                    [
                        'url' => [
                            'name' => '', // 链接名称
                            'value' => '', // 链接
                        ],
                        'title' => '', // 搜索提示词
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
        $is_show_validate_string = Constant::ONE.','.Constant::ZERO;
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_HOME_NAV,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required|array',
            'content.logo' => 'present|nullable',
            'content.keywords' => 'present|nullable',
            'content.button_color' => 'present|nullable',
            'content.interval' => 'required|min:1|max:10',
            'content.data' => 'present|nullable|array',
            'content.data.*.title' => 'present|nullable',
            'content.data.*.url.name' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
        ], $this->messages());

        if ($validator->fails()) {
            throw new ProcessDataException('搜索组件'.'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }

        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data['name'] = '搜索';
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_HOME_NAV;
        $data['is_fixed_assembly'] = $this->fixed_assembly_yes;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];

        return [
            'component_name' => $data['component_name'],
            'logo' => $content['logo'] ?? '',
            'keywords' => $content['keywords'] ?? '', // 关键词
            'button_color' => $content['button_color'] ?? '', // 搜索按钮背景色（默认#f71111）
            'interval' => $content['interval'] ?? 1, // 关键词轮播时间，最大10 最小1
            'items' => $content['data'] ?? [],
        ];
    }

    /**
     * 商家后台数据渲染.
     */
    public function display($data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];

        return [
            'id' => $data['id'] ?? 0, // 组件自增id
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? Constant::ONE, // 是否展示 1展示0不展示
            'sort' => $data['sort'] ?? Constant::ONE, // 排序
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? Constant::ZERO, // 是否是固定组件 1是 0否
            'content' => $content,
            'data' => $this->getContent($data),
        ];
    }

    /**
     * 返回验证信息.
     */
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
            'content.logo.present' => 'logo 参数未设置',
            'content.keywords.present' => '关键词 参数未设置',
            'content.button_color.present' => '按钮背景色 参数未设置',
            'content.interval.required' => '请设置关键词轮播时间',
            'content.interval.min' => '关键词轮播时间不能小于 1 秒',
            'content.interval.max' => '关键词轮播时间不能大于 10 秒',
            'content.data.present' => '内容板块不存在',
            'content.data.array' => '内容数据格式不正确',
            'content.data.*.title.present' => '搜索提示词 参数未设置',
            'content.data.*.url.name.present' => 'url链接别名 参数未设置',
            'content.data.*.url.value.present' => 'url链接 参数未设置',
        ];
    }
}
