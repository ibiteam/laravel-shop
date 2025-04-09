<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class AdvertComponent extends PageComponent
{
    /**
     * 时间类型： 1 长期  0：时间范围
     */
    private int $long_time_yes = 1; // 长期


    /**
     * 是否是固定组件 1是 0否
     */
    private int $fixed_assembly_yes = 1;


    public function icon(): array
    {
        $publicData = $this->getPublicData();
        return [
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'sort' => '',
        ];
    }


    public function parameter(): array
    {
        $publicData = $this->getPublicData();
        return [
            'id' => '',// 组件自增id
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_yes, // 是否是固定组件 1是 0否
            'content' => [
                'image' => '', // 图片地址
                'url' => [
                    'name' => '', // 链接名称
                    'value' => '', // 链接
                ],
                'describe' => '', // 描述
                'is_show' => Constant::ONE, // 是否显示 1展示 0隐藏
                'date_type' => $this->long_time_yes, // 时间类型： 1 长期  0：时间范围
                'time' => [],
            ],
        ];
    }


    private function getPublicData()
    {
        $component_name = $this->getComponentName() ?: AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT;
        switch ($component_name) {
            case AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT:
                return [
                    'component_name' => $component_name,
                    'name' => '弹屏广告'
                ];
            case AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT:
                return [
                    'component_name' => $component_name,
                    'name' => '悬浮广告'
                ];
        }
    }


    /**
     * @param $data
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        $publicData = $this->getPublicData();
        if (isset($data['content']['is_show']) && $data['content']['is_show'] == Constant::ONE) {
            $required = 'required';
        } else {
            $required = 'nullable';
        }
        $is_show_validate_string = Constant::ONE . ',' . Constant::ZERO;
        $component_name_string = AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT . ',' . AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT;
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:' . $component_name_string,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.image' => $required,
            'content.describe' => 'nullable',
            'content.url.name' => 'present|nullable',
            'content.url.value' => 'present|nullable',
            'content.is_show' => 'required|in:'.$is_show_validate_string,
            'content.date_type' => 'present|in:'.$is_show_validate_string,
            'content.time' => [
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
        ], $this->messages());

        if ($validator->fails()) {
            throw new ProcessDataException($publicData['name'].'：'.$validator->errors()->first(), ['id' => $validator['id']]);
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data['name'] = $publicData['name'];
        $data['component_name'] = $publicData['component_name'];
        $data['is_fixed_assembly'] = $this->fixed_assembly_yes;
        return $data;
    }


    /**
     * 返回验证信息
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
            'content.image.required' => '请上传图片',
            'content.url.name.present' => 'url链接别名参数未设置',
            'content.url.value.present' => '请设置url链接别名值参数',
            'content.date_type.present' => '时间类型参数未设置',
            'content.date_type.in' => '时间类型板块参数格式不正确',
            'content.time.array' => '时间字段必须是一个数组',
            'content.time.size' => '时间字段必须包含两个时间值',
            'content.time.custom' => '时间字段格式不正确或时间范围无效',
        ];
    }


    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $time = date('Y-m-d H:i:s');
        // 不展示的数据和时间过期的 都 不展示
        if ($content['is_show'] == Constant::ZERO) {
            $content = [];
        } else if ($content['date_type'] != $this->long_time_yes && $content['time'][0] > $time && $content['time'][1] < $time ) {
            $content = [];
        }

        return [
            'component_name' => $data['component_name'],
            'items' => $content,
        ];
    }


    /**
     * 商家后台数据渲染
     * @param $data
     * @return array
     */
    public function display($data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];

        return [
            'id' => $data['id'] ?? 0,// 组件自增id
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? Constant::ONE, // 是否展示 1展示0不展示
            'sort' => $data['sort'] ?? Constant::ONE, // 排序
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? Constant::ZERO, // 是否是固定组件 1是 0否
            'content' => $content,
            'data' => $this->getContent($data),
        ];
    }
}
