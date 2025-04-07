<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class HorizontalCarouselComponent extends PageComponent
{
    /**
     * 是否是固定组件 1是 0否.
     */
    private int $fixed_assembly_no = 0;

    /**
     * 时间类型： 1 长期  0：时间范围.
     */
    private int $long_time_yes = 1; // 长期

    /**
     * 图片 尺寸.
     */
    private int $one_width = 710;
    private int $min_height = 200;
    private int $max_height = 350;

    public function icon(): array
    {
        $publicData = $this->getPublicData();

        return [
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'limit' => 0,
            'icon' => $publicData['icon'],
            'sort' => '',
        ];
    }

    // 轮播图默认数据
    public function parameter(): array
    {
        $publicData = $this->getPublicData();

        return [
            'id' => '', // 组件自增id
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_no, // 是否是固定组件 1是 0否
            'content' => [
                'width' => $publicData['width'], // 宽度：不支持修改
                'height' => $publicData['height'],
                'style' => $publicData['style'],  // 显示样式：默认 1、平铺 2、过渡
                'interval' => $publicData['interval'],
                'data' => [
                    [
                        'url' => [
                            'name' => '', // 链接名称
                            'value' => '', // 链接
                        ],
                        'image' => '', // 图片地址
                        'sort' => Constant::ONE, // 排序 （1~100）
                        'is_show' => Constant::ONE, // 是否显示 1展示 0隐藏
                        'date_type' => $this->long_time_yes, // 时间类型： 1 长期  0：时间范围
                        'time' => [],
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
        $publicData = $this->getPublicData();
        $validate_data = [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required|array',
            'content.height' => 'required|numeric|between:'.$this->min_height.','.$this->max_height,
            'content.width' => 'required|numeric|in:'.$this->one_width,
            'content.style' => 'required|in:'.AppDecorationItem::STYLE_TILED.','.AppDecorationItem::STYLE_TRANSITION,
            'content.interval' => 'nullable|integer',
            'content.data' => 'required|array',
            'content.data.*.url.name' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            'content.data.*.image' => 'required',
            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
            'content.data.*.is_show' => 'required|in:'.$is_show_validate_string,
            'content.data.*.date_type' => 'present|in:'.$is_show_validate_string,
            'content.data.*.time' => [
                'array', // 确保 time 是数组
                'size:2', // 确保数组长度为 2
                function ($attribute, $value, $fail) {
                    // 检查两个时间是否为有效日期时间格式
                    if (! is_array($value) || count($value) !== 2) {
                        return $fail('时间字段必须是一个包含两个时间的数组');
                    }

                    $start_time = $value[0] ?? null;
                    $end_time = $value[1] ?? null;

                    if (! $start_time || ! $end_time) {
                        return $fail('时间字段不能为空');
                    }

                    if (! strtotime($start_time) || ! strtotime($end_time)) {
                        return $fail('时间字段必须是有效的日期时间格式');
                    }

                    // 检查开始时间是否早于或等于结束时间
                    if (strtotime($start_time) > strtotime($end_time)) {
                        return $fail('开始时间不能晚于结束时间');
                    }
                },
            ],
        ];
        $validator = Validator::make($data, $validate_data, $this->messages());

        if ($validator->fails()) {
            throw new BusinessException($publicData['name'].'：'.$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();

        $data['content']['data'] = collect($data['content']['data'])->sortByDesc('sort')->values()->toArray();
        $data['component_name'] = $publicData['component_name'];
        $data['is_fixed_assembly'] = $this->fixed_assembly_no;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $publicData = $this->getPublicData();
        $content = $data['content'];
        $items = collect($content['data'])
            ->map(function ($item) {
                if ($item['is_show'] == Constant::ZERO) {
                    return null;
                }

                return $item;
            })
            ->sortByDesc('sort')
            ->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'width' => $publicData['width'],
            'height' => $publicData['height'],
            'interval' => $publicData['interval'],
            'style' => $publicData['style'],
            'items' => $items,
        ];
    }

    /**
     * 后台数据渲染.
     */
    public function display($data): array
    {
        // 默认值
        if (empty($data)) {
            return $this->display($this->parameter());
        }

        return [
            'id' => $data['id'] ?? 0, // 组件自增id
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? Constant::ONE, // 是否展示 1展示0不展示
            'sort' => $data['sort'] ?? Constant::ONE, // 排序
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? Constant::ZERO, // 是否是固定组件 1是 0否
            'content' => $data['content'] ?? [],
            'data' => $this->getContent($data),
        ];
    }

    private function getPublicData(): array
    {
        // 获取轮播图认数据
        $component_name = $this->getComponentName() ?: AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL;

        return [
            'component_name' => $component_name,
            'width' => $this->one_width,
            'height' => $this->min_height,
            'name' => '轮播图',
            'icon' => '&#xe7a6;',
            'style' => 1, // 显示样式 1、平铺 2、过度
            'interval' => 3, // 切换时间 单位秒
        ];
    }

    /**
     * 验证信息返回.
     */
    private function messages(): array
    {
        return [
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID 不正确',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过 :100 个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => '板块数据格式不正确',
            'content.interval.integer' => '切换时间类型错误',
            'content.height.required' => '请输入图片高度',
            'content.width.required' => '请输入图片宽度',
            'content.width.in' => '图片宽度是710px',
            'content.height.between' => '图片高度范围是200px~350px',
            'content.data.array' => '板块数据格式不正确',
            'content.data.*.is_show.required' => '请设置内容是否展示',
            'content.data.*.is_show.in' => '板块是否展示参数格式不正确',
            'content.data.*.image.required' => '请上传图片',
            'content.data.*.url.name.present' => 'url链接别名参数未设置',
            'content.data.*.url.value.present' => '请设置url链接别名值参数',
            'content.data.*.sort.integer' => '排序参数格式不正确',
            'content.data.*.sort.max' => '排序最大值是100',
            'content.data.*.sort.min' => '排序最小值是1',
            'content.data.*.date_type.present' => '时间类型参数未设置',
            'content.data.*.date_type.in' => '时间类型板块参数格式不正确',
            'content.data.*.time.array' => '时间字段必须是一个数组',
            'content.data.*.time.size' => '时间字段必须包含两个时间值',
            'content.data.*.time.custom' => '时间字段格式不正确或时间范围无效',
        ];
    }
}
