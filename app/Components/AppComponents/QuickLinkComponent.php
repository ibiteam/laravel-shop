<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class QuickLinkComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => $this->getName() ?: '金刚区',
            'component_name' => $this->getComponentName(),
            'limit' => 0,
            'icon' => "&#xe7ce;",
            'sort' => "",
        ];
    }

    public function parameter(): array
    {
        return [
            'id' => '', //组件自增id
            'name' => $this->getName() ?: '金刚区',
            'component_name' => $this->getComponentName(),// 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
            'sort' => Constant::ZERO,
            'content' => [
                'row' => AppDecorationItem::PLATE_HEIGHT_ONE, // 板块行数
                'column' => AppDecorationItem::NUMBER_ROWS_THREE, // 每行显示
                'data' => [
                    [
                        'image' => '', // 图片地址
                        'title' => '', // 名称
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'sort' => 1, // 排序
                        'is_show' => Constant::ONE, // 是否展示
                    ],
                    [
                        'image' => '', // 图片地址
                        'title' => '', // 名称
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'sort' => 2, // 排序
                        'is_show' => Constant::ONE, // 是否展示
                    ],
                    [
                        'image' => '', // 图片地址
                        'title' => '', // 名称
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'sort' => 3, // 排序
                        'is_show' => Constant::ONE, // 是否展示
                    ],
                    [
                        'image' => '', // 图片地址
                        'title' => '', // 名称
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'sort' => 4, // 排序
                        'is_show' => Constant::ONE, // 是否展示
                    ],
                ],
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
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_QUICK_LINK,
            'is_show' => 'required|integer|in:'. Constant::ONE.','.Constant::ZERO,
            'content.row' => 'required|in:'. AppDecorationItem::PLATE_HEIGHT_ONE.','.AppDecorationItem::PLATE_HEIGHT_TWO.','.AppDecorationItem::PLATE_HEIGHT_THREE, // 每行展示 1行 2行 3行
            'content.column' => 'required|in:'. AppDecorationItem::NUMBER_ROWS_THREE.','.AppDecorationItem::NUMBER_ROWS_FOUR.','.AppDecorationItem::NUMBER_ROWS_FIVE, // 每行个数 3个 4个 5个
            'content.data' => 'required|array',
            'content.data.*.image' => 'required',
            'content.data.*.title' => 'max:6',
            'content.data.*.url.name' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
            'content.data.*.is_show' => 'required|int:' . Constant::ONE . ',' . Constant::ZERO,
        ], $this->messages());
        if ($validator->fails()) {
            throw new ProcessDataException($this->getName().'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }
        $title = array_column($data['content']['data'], 'title');
        $title_arr = $this->checkUnique($title);
        if ( $title_arr ) {
            throw new ProcessDataException($this->getName().'：'.implode('，' ,$title_arr) .'，名称已存在，请修改！', ['id' => $data['id']]);
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data['name'] = '金刚区';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_QUICK_LINK;

        return $data;
    }

    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $items = collect($content['data'])->sortByDesc('sort')->map(function ($item) use (&$items) {
            $data['image'] = $item['image'] ?? '';
            $data['title'] = $item['title'] ?? '';
            $data['url'] = $item['url'];
            if ($item['is_show']) {
                return $data;
            }

            return null;
        })->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'row' => $content['row'] ?? AppDecorationItem::PLATE_HEIGHT_ONE,
            'column' => $content['column'] ?? AppDecorationItem::NUMBER_ROWS_THREE,
            'items' => $items,
        ];
    }

    /**
     * 后台数据渲染
     * @param $data
     * @return array
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
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过100个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.row.required' => '请选择每行展示',
            'content.row.in' => '每行展示数据格式不正确',
            'content.column.required' => '请选择每行个数',
            'content.column.in' => '每行个数数据格式不正确',
            'content.data.required' => '请设置板块对应数据',
            'content.data.*.image.required' => '请上传图片',
            'content.data.*.title.max' => '名称不能超过6个字符',
            'content.data.*.url.name.present' => 'url链接别名参数未设置',
            'content.data.*.url.value.present' => '请设置url链接别名值参数',
            'content.data.*.sort.integer' => '排序必须为整数',
            'content.data.*.sort.min' => '排序最小值是1',
            'content.data.*.sort.max' => '排序最大值是100',
            'content.data.*.is_show.required' => '是否显示不能为空',
        ];
    }

    private function checkUnique($array): array
    {
        $counts = array_count_values($array);
        // 遍历统计结果，找出重复的字段名
        $duplicateFields = [];
        foreach ($counts as $value => $count) {
            if ($count > 1) {
                $duplicateFields[] = $value;
            }
        }

        return $duplicateFields;
    }
}
