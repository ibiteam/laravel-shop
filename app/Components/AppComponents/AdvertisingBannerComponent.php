<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Services\AppDecoration\AppDecorationItemService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class AdvertisingBannerComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => $this->getName() ?: '广告图',
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
            'name' => $this->getName() ?: '广告图',
            'component_name' => $this->getComponentName(), // 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
            'sort' => Constant::ZERO,
            'content' => [
                'column' => AppDecorationItem::NUMBER_COLUMN_TWO, // 每行显示
                'background' => AppDecorationItem::BACKGROUND_COLOR_SHOW, // 是否展示背景色 1、有 0、无
                'background_color' => '#ffffff', // 背景色 默认 白色
                'width' => 340, // 宽度：默认330 不可修改 2个330,3个220,4个160
                'height' => 190, // 高度：默认240（最高250）；2个240,3个150，4个150
                'title' => [
                    'image' => '', // 图片地址
                    'name' => '', // 标题
                    'align' => 'left', // 标题对齐，默认左对齐，left-左侧，center-居中
                    'suffix' => '', // 标题右侧文案
                    'color' => '#333333', // 标题颜色
                    'url' => [
                        'name' => '',
                        'value' => '',
                    ],
                ],
                'data' => [
                    [
                        'url' => [
                            'name' => '',
                            'value' => '',
                        ],
                        'date_type' => AppDecorationItem::LONG_TIME, // 1、长期 0、时间范围
                        'time' => [], //
                        'image' => '', // 图片地址
                        //                        'sort' => 1, // 排序
                        'is_show' => Constant::ONE, // 是否展示
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
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_ADVERTISING_BANNER,
            'is_show' => 'required|integer|in:'.Constant::ONE.','.Constant::ZERO,
            'content.column' => 'required|in:'.AppDecorationItem::NUMBER_COLUMN_TWO.','.AppDecorationItem::NUMBER_COLUMN_THREE.','.AppDecorationItem::NUMBER_COLUMN_FOUR, // 每行个数 2、3、4
            'content.background' => 'required|in:'.AppDecorationItem::BACKGROUND_COLOR_SHOW.','.AppDecorationItem::BACKGROUND_COLOR_NOT_SHOW,
            'content.background_color' => 'required_if:content.background,'.AppDecorationItem::BACKGROUND_COLOR_SHOW,
            'content.width' => 'required',
            'content.height' => 'required',
            'content.title' => 'required|array',
            'content.title.image' => 'present|nullable',
            'content.title.name' => 'present|nullable',
            'content.title.align' => 'present|nullable',
            'content.title.suffix' => 'present|nullable',
            'content.title.color' => 'present|nullable',
            'content.title.url.name' => 'present|nullable',
            'content.title.url.value' => 'present|nullable',
            'content.data' => 'required|array',
            'content.data.*.image' => 'required',
            'content.data.*.url.name' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            //            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
            'content.data.*.is_show' => 'required|in:'.$is_show_validate_string,
            'content.data.*.date_type' => 'present|in:'.$is_show_validate_string,
            'content.data.*.time' => 'array',
        ], $this->messages());

        if ($validator->fails()) {
            throw new ProcessDataException($this->getName().'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }

        // 检查 每行固定展示个数的时候，宽度和高度是否达标
        if ($msg = $this->checkColumn($data['content'])) {
            throw new ProcessDataException($this->getName().'：'.$msg, ['id' => $data['id']]);
        }
        // 独立校验 time 字段
        foreach ($data['content']['data'] as $index => $item) {
            $date_type = $item['date_type'] ?? null;
            $time = $item['time'] ?? null;

            if ($date_type == AppDecorationItem::CUSTOM_TIME) {
                app(AppDecorationItemService::class)->validateTimeFields($this->getName(), $time, $data['id'], $index);
            }
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data['name'] = '广告图';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_ADVERTISING_BANNER;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $items = collect($content['data'])
//            ->sortByDesc('sort')
            ->map(function ($item) use (&$items) {
                if (!$item['is_show']) {
                    return null;
                }
                $time = date('Y-m-d H:i:s');
                if ($item['date_type'] == AppDecorationItem::CUSTOM_TIME) {
                    $start_time = $item['time'][0] ?? '';
                    $end_time = $item['time'][1] ?? '';
                    if ($start_time > $time || $end_time < $time) {
                        return null;
                    }
                }

                $data['image'] = $item['image'] ?? '';
                $data['url'] = $item['url'];
                $data['date_type'] = $item['date_type'];
                $data['time'] = $item['time'];
                $data['sort'] = $item['sort'] ?? 0;

                return $data;
            })->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'column' => $content['column'] ?? AppDecorationItem::NUMBER_COLUMN_TWO,
            'background' => $content['background'] ?? AppDecorationItem::BACKGROUND_COLOR_SHOW,
            'background_color' => $content['background_color'] ?? '#ffffff',
            'width' => $content['width'],
            'height' => $content['height'],
            'title' => $content['title'],
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
            'sort' => $data['sort'] ?? 0,
            'content' => $data['content'] ?? null, // 表单提交数据
            'data' => $this->getContent($data), // 战术数据
        ];
    }

    // 检查行数和宽度是否匹配
    private function checkColumn($content)
    {
        $data_count = count($content['data']);
        $width = $content['width'];
        $height = $content['height'];
        $background = $content['background'];
        $column = $content['column'];

        switch ($column) {
            case AppDecorationItem::NUMBER_COLUMN_TWO:
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_SHOW && $width !== 340) {
//                    return "每行显示 {$column}，有背景图的情况下，宽度只能是340";
//                }
//
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_NOT_SHOW && $width !== 350) {
//                    return "每行显示 {$column}，无背景图的情况下，宽度只能是350";
//                }

                if ($height < 190 || $height > 250) {
                    return "每行显示 {$column}，高度范围 190 - 250";
                }

                // 如果数据的个数不是2的倍数
                if ($data_count % 2 !== 0 || $data_count > 8) {
                    return "每行显示 {$column}，内容个数必须是 2 的倍数且最多为 8 个";
                }

                break;

            case AppDecorationItem::NUMBER_COLUMN_THREE:
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_SHOW && $width !== 220) {
//                    return "每行显示 {$column}，有背景图的情况下，宽度只能是220";
//                }
//
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_NOT_SHOW && $width !== 230) {
//                    return "每行显示 {$column}，无背景图的情况下，宽度只能是230";
//                }

                if ($height < 280 || $height > 400) {
                    return "每行显示 {$column}，高度范围 280 - 400";
                }

                // 如果数据的个数不是2的倍数
                if ($data_count % 3 !== 0 || $data_count > 12) {
                    return "每行显示 {$column}，容个数必须是3的倍数且最多为12个";
                }

                break;

            case AppDecorationItem::NUMBER_COLUMN_FOUR:
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_SHOW && $width !== 160) {
//                    return "每行显示 {$column}，有背景图的情况下，宽度只能是160";
//                }
//
//                if ($background == AppDecorationItem::BACKGROUND_COLOR_NOT_SHOW && $width !== 170) {
//                    return "每行显示 {$column}，无背景图的情况下，宽度只能是170";
//                }

                if ($height < 220 || $height > 350) {
                    return "每行显示 {$column}，高度范围 220 - 350";
                }

                // 如果数据的个数不是4的倍数
                if ($data_count % 4 !== 0 || $data_count > 16) {
                    return "每行显示 {$column}，内容个数必须是 4 的倍数且最多为 12 个";
                }
        }

        return false;
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

            // content.column 验证
            'content.column.required' => '每行展示数量不能为空',
            'content.column.in' => '每行展示数量必须是 2、3 或 4',

            // content.background 验证
            'content.background.required' => '背景状态不能为空',
            'content.background.in' => '背景状态无效，只能是有或无',

            // content.background_color 验证
            'content.background_color.required_if' => '当背景状态为显示时，背景颜色不能为空',

            // content.width 和 content.height 验证
            'content.width.required' => '宽度不能为空',
            'content.height.required' => '高度不能为空',

            // content.title 验证
            'content.title.required' => '标题配置不能为空',
            'content.title.array' => '标题配置必须是一个数组',
            'content.title.image.present' => '标题图片地址参数未设置',
            'content.title.name.present' => '标题名称参数未设置',
            'content.title.align.present' => '标题对齐方式参数未设置',
            'content.title.suffix.present' => '标题后缀参数未设置',
            'content.title.color.present' => '标题颜色参数未设置',
            'content.title.url.name.present' => '标题链接别名参数未设置',
            'content.title.url.value.present' => '标题链接值参数未设置',

            // content.data 验证
            'content.data.required' => '数据列表不能为空',
            'content.data.*.image.required' => '图片地址不能为空',
            'content.data.*.url.name.present' => '链接别名参数未设置',
            'content.data.*.url.value.present' => '链接值参数未设置',
            'content.data.*.sort.integer' => '排序必须是整数',
            'content.data.*.sort.min' => '排序最小值是1',
            'content.data.*.sort.max' => '排序最大值是100',
            'content.data.*.is_show.required' => '是否显示不能为空',
            'content.data.*.is_show.in' => '是否显示的值只能是 0 或 1',
            'content.data.*.date_type.present' => '日期类型参数未设置',
            'content.data.*.date_type.in' => '日期类型无效，只能是长期或时间范围',
            'content.data.*.time.array' => '时间字段必须是一个数组',
        ];
    }
}
