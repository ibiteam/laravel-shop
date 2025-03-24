<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Services\MobileRouterService;
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
            'id' => '',//组件自增id
            'name' => $this->getName() ?: '金刚区',
            'component_name' => $this->getComponentName(),// 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => Constant::ZERO,
            'content' => [
                'style' => '1', // 轮播样式
                'height' => '2', // 板块高度
                'number' => '3', // 每行个数
                'items' => [
                    [
                        'image' => '', // 图片地址
                        'gif' => '', // 动图地址
                        'title' => '', // 名称
                        'url' => [
                            'alias' => 'https',
                            'value' => '',
                            'default_selection_data' => [],
                        ],
                        'sort' => '1', // 排序
                        'is_app' => Constant::ONE, // 是否展示在APP
                        'is_h5' => Constant::ONE, // 是否展示在 H5
                        'is_mini' => Constant::ONE, // 是否展示在小程序
                    ]
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
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK,
            'is_show' => 'required|integer|in:'. Constant::ONE.','.Constant::ZERO,
            'content.style' => 'required|in:'. AppWebsiteDecorationItem::HORIZONTAL_SCROLLING.','.AppWebsiteDecorationItem::FULL_SCREEN_FLIPPING, // 轮播样式 1、横向滚动 2、整屏翻页
            'content.height' => 'required|in:'. AppWebsiteDecorationItem::PLATE_HEIGHT_ZERO.','.AppWebsiteDecorationItem::PLATE_HEIGHT_ONE.','.AppWebsiteDecorationItem::PLATE_HEIGHT_TWO.','.AppWebsiteDecorationItem::PLATE_HEIGHT_THREE, // 板块高度 0、不限制 1、1行 2、2行 3、3行
            'content.number' => 'required|in:'. AppWebsiteDecorationItem::NUMBER_ROWS_THREE.','.AppWebsiteDecorationItem::NUMBER_ROWS_FOUR.','.AppWebsiteDecorationItem::NUMBER_ROWS_FIVE, // 每行个数 1、3个 2、4个 3、5个
            'content.items' => 'required|array',
            'content.items.*.image' => 'required',
            'content.items.*.gif' => 'nullable',
            'content.items.*.title' => 'max:6',
            'content.items.*.url.alias' => 'present|nullable',
            'content.items.*.url.value' => 'present|nullable',
            'content.items.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
            'content.items.*.is_app' => 'required|boolean',
            'content.items.*.is_h5' => 'required|boolean',
            'content.items.*.is_mini' => 'required|boolean',
        ], $this->messages());
        if ($validator->fails()) {
            throw new BusinessException($this->getName() . $validator->errors()->first());
        }
        $title = array_column($data['content']['items'], 'title');
        $title_arr = $this->checkUnique($title);
        if ( $title_arr ) {
            throw new BusinessException( implode('，' ,$title_arr) .'，名称已存在，请修改！');
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $mobile_router_service = (new MobileRouterService());
        collect($data['content']['items'])->map(function($item, $key) use($mobile_router_service){
            try {
                $mobile_router_service->viodData($item['url']['alias'] ?? '', $item['url']['value'] ?? '');
            } catch (\Exception $exception) {
                throw new BusinessException('金刚区数据：第'.($key + 1).'个菜单中，'.$exception->getMessage());
            }
        });
        $data['name'] = '金刚区';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK;

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
        $source = $data['source'] ?? MobileRouterService::SOURCE_APP;
        $mobileRouterService = app(MobileRouterService::class);
        $items = collect($content['items'])->sortByDesc('sort')->map(function ($item) use (&$items, $source, $mobileRouterService) {
            $data['image'] = $item['image'] ?? '';
            $data['gif'] = $item['gif'] ?? '';
            $data['title'] = $item['title'] ?? '';
            if (($item['url']['alias'] ?? '')) {
                $urls = $mobileRouterService->handleUrl($item['url']['alias'], $item['url']['value'] ?? '', $source);
            }
            $data['url'] = $urls ?? '';
            $data['url_alias'] = $item['url']['alias'] ?? '';
            if ( MobileRouterService::SOURCE_MINI == $source && $item['is_mini']) {
                return $data;
            } elseif (MobileRouterService::SOURCE_H5 == $source && $item['is_h5']){
                return $data;
            } elseif(MobileRouterService::SOURCE_APP == $source && $item['is_app']) {
                if(is_harmony_request() &&  in_array($data['url_alias'],Router::$harmony_no_show)){
                    return null;
                }else{
                    return $data;
                }

            }

            return null;
        })->filter()->values()->toArray();
//        if (AppWebsiteDecorationItem::PLATE_HEIGHT_ZERO != $content['height']) {
//            switch ($content['style']) {
//                case AppWebsiteDecorationItem::HORIZONTAL_SCROLLING:
//                    $items = array_chunk($items, $content['height']);
//
//                    break;
//                case AppWebsiteDecorationItem::FULL_SCREEN_FLIPPING:
//                    $number = AppWebsiteDecorationItem::$numbers_rows[$content['number']];
//                    $items = array_chunk(array_chunk($items, $content['height']), $number);
//
//                    break;
//            }
//        }

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'style' => $content['style'] ?? AppWebsiteDecorationItem::HORIZONTAL_SCROLLING,
            'height' => $content['height'] ?? AppWebsiteDecorationItem::PLATE_HEIGHT_ONE,
            'number' => $content['number'] ? AppWebsiteDecorationItem::$numbers_rows[$content['number']] : AppWebsiteDecorationItem::NUMBER_ROWS_THREE,
            'items' => $items,
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
        $content = $data['content'];
        $mobileRouterService = app(MobileRouterService::class);
        $content['items'] = collect($content['items'])->map(function($item) use ($mobileRouterService) {
            $item['url']['default_selection_data'] = $mobileRouterService->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);

            return $item;
        })->toArray();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'sort' => $data['sort'],
            'content' => $content ?? null, // 表单提交数据
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
            'content.style.required' => '请选择轮播样式',
            'content.style.in' => '轮播样式数据格式不正确',
            'content.height.required' => '请选择板块高度',
            'content.height.in' => '板块高度数据格式不正确',
            'content.number.required' => '请选择每行个数',
            'content.number.in' => '每行个数数据格式不正确',
            'content.items.required' => '请设置板块对应数据',
            'content.items.*.image.required' => '请上传图片',
            'content.items.*.title.max' => '名称不能超过6个字符',
            'content.items.*.url.alias.present' => 'url链接别名参数未设置',
            'content.items.*.url.value.present' => '请设置url链接别名值参数',
            'content.items.*.sort.integer' => '排序必须为整数',
            'content.items.*.sort.min' => '排序最小值是1',
            'content.items.*.sort.max' => '排序最大值是100',
            'content.items.*.is_app.required' => '是否显示在APP端不能为空',
            'content.items.*.is_h5.required' => '是否显示在H5端不能为空',
            'content.items.*.is_mini.required' => '是否显示在小程序不能为空',
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
