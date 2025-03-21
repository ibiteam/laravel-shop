<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Services\MobileRouterService;
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
                    'alias' => 'https', // 链接类型
                    'value' => '', // 链接
                    'default_selection_data' => [], //选中的数据
                ],
                'is_show' => Constant::ZERO, // 是否显示 1展示 0隐藏
                'date' => [
                    'type' => $this->long_time_yes, // 时间类型： 1 长期  0：时间范围
                    'value' => [
                        'start_time' => '',
                        'end_time' => '',
                    ],
                ],

            ],
        ];
    }


    private function getPublicData()
    {
        $component_name = $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN;
        switch ($component_name) {
            case AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN:
                return [
                    'component_name' => $component_name,
                    'name' => '大屏广告位'
                ];
            case AppWebsiteDecorationItem::COMPONENT_NAME_RED_ENVELOPE:
                return [
                    'component_name' => $component_name,
                    'name' => '签到送红包'
                ];
            case AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING:
                return [
                    'component_name' => $component_name,
                    'name' => '侧边广告位'
                ];
            case AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT:
                return [
                    'component_name' => $component_name,
                    'name' => '二楼广告位'
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
        $start_time = 'sometimes';
        $end_time = 'sometimes';
        if (isset($data['content']['date']['type']) && $data['content']['date']['type'] == Constant::ZERO) {
            $start_time = 'required_without:end_time';
            $end_time = 'required_without:start_time';
        }

        if (isset($data['content']['is_show']) && $data['content']['is_show'] == Constant::ONE) {
            $required = 'required';
        } else {
            $required = 'nullable';
        }
        $is_show_validate_string = Constant::ONE . ',' . Constant::ZERO;
        $component_name_string = AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN . ',' . AppWebsiteDecorationItem::COMPONENT_NAME_RED_ENVELOPE . ',' . AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING . ',' . AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT;
        $validator = Validator::make($data, [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:' . $component_name_string,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.image' => $required,
            'content.url.alias' => 'present|nullable',
            'content.url.value' => 'present|nullable',
            'content.date' => 'array',
            'content.date.type' => 'required|integer|in:' . $is_show_validate_string,
            'content.is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content.date.value.start_time' => $start_time,
            'content.date.value.end_time' => $end_time,
        ], $this->messages());

        if ($validator->fails()) {
            throw new BusinessException($publicData['name'] . '：' . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();


        // 显示的时候校验
        if ($data['content']['is_show'] == Constant::ONE) {
            //校验url链接的正确性
            $mobile_router_service = new MobileRouterService();
            try {
                $mobile_router_service->viodData($data['content']['url']['alias'] ?? '', $data['content']['url']['value'] ?? '');
            } catch (\Exception $exception) {
                throw new BusinessException($publicData['name'] . '：' . '菜单数据，' . $exception->getMessage());
            }
        }



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
            'content.url.alias.present' => 'url链接别名参数未设置',
            'content.url.value.present' => '请设置url链接别名值参数',
            'content.date.array' => '板块数据格式不正确',
            'content.date.value.start_time.required_without' => '请选择显示时间',
            'content.date.value.end_time.required_without' => '请选择显示时间',
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
        $source = $data['source'] ?? null;
        $mobileRouterService = app(MobileRouterService::class);
        $urls = '';
        if (($content['url']['alias'] ?? '')) {
            $urls = $mobileRouterService->handleUrl($content['url']['alias'], $content['url']['value'] ?? '', $source);
        }


        // 数据重组
        $new_data = [];
        if ($content['is_show'] == Constant::ONE) {
            $now_time = date('Y-m-d H:i:s');
            if (isset($content['date']['type'])) {
                if (
                    ($content['date']['type'] == Constant::ONE)
                    || (
                        ($content['date']['type'] == Constant::ZERO)
                        && ($now_time >= $content['date']['value']['start_time'])
                        && ($now_time <= $content['date']['value']['end_time'])
                    )
                ) {
                    $new_data['sort'] = $data['sort'] ?? 1;
                    $new_data['component_name'] = $data['component_name'] ?? '';
                    $new_data['icon'] = $content['image'] ?? '';
                    $new_data['url'] = $urls;
                    $new_data['url_alias'] = $content['url']['alias'] ?? '';

                    return $new_data;
                }
            }
        }
        return $new_data;
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
        $mobile_router_service = new MobileRouterService();
        $content['url']['default_selection_data'] = $mobile_router_service->getOption($content['url']['alias'] ?? '', $content['url']['value'] ?? '', true);


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
