<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Models\ShopConfig;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class ThemeAdvertisingComponent extends PageComponent
{
    /**
     * 是否是固定组件 1是 0否
     */
    private int $fixed_assembly_no = 0;


    /**
     * 默认尺寸
     */
    private int $width = 330;
    private int $height = 150;


    public function icon(): array
    {
        return [
            'name' => '主题广告',
            'component_name' => $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING,
            'limit' => 0,
            'icon' => '&#xe7dc;',
            'sort' => '',
        ];
    }


    public function parameter(): array
    {
        return [
            'id' => '',// 组件自增id
            'name' => $this->getName() ?: '主题广告',
            'component_name' => $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING,
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_no, // 是否是固定组件 1是 0否
            'content' => [
                'name' => '', // 板块名称
                'url' => [
                    'alias' => 'search', // 链接类型
                    'value' => '', // 链接
                    'default_selection_data' => [], //选中的数据
                ],
                'width' => $this->width, // 宽度：不支持修改
                'height' => $this->height,
                'data' => [
                    [
                        'image' => '', //图片地址
                        'url' => [
                            'alias' => 'https', // 链接类型
                            'value' => '', // 链接
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => 1, // 排序 （1~100）
                        'is_show' => 1 // 是否显示 1展示 0隐藏
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
        $is_show_validate_string = Constant::ONE . ',' . Constant::ZERO;
        $validator = Validator::make($data, [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:' . AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.name' => 'required|max:10', // 板块名称
            'content.url.alias' => 'present|nullable',
            'content.url.value' => 'present|nullable',
            'content.height' => 'required|numeric|between:150,200',
            'content.width' => 'required|in:330',
            'content.data' => 'required|array',
            'content.data.*.image' => 'required',
            'content.data.*.is_show' => 'required|in:' . $is_show_validate_string,
            'content.data.*.url.alias' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
        ], $this->messages());

        if ($validator->fails()) {
            $name = '主题广告';
            throw new BusinessException($name . '：' . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();


        //校验url链接的正确性
        $mobile_router_service = new MobileRouterService();
        try {
            $mobile_router_service->viodData($data['content']['url']['alias'] ?? '', $data['content']['url']['value'] ?? '');
        } catch (\Exception $exception) {
            throw new BusinessException(($this->getName() ?: '主题广告') . '：' .$exception->getMessage());
        }


        $data['content']['data'] = collect($data['content']['data'])->map(function ($item, $key) use ($mobile_router_service) {
			if($item['is_show']){
				try {
					$mobile_router_service->viodData($item['url']['alias'] ?? '', $item['url']['value'] ?? '');
				} catch (\Exception $exception) {
					throw new BusinessException(($this->getName() ?: '主题广告') . '：' . '菜单数据，第'.($key + 1).'条，'.$exception->getMessage());
				}
			}

            $item['sort'] = (int) $item['sort'] ?: 1;
            return $item;
        })->sortByDesc('sort')->values()->toArray();


        $data ['component_name'] = $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING;
        $data ['is_fixed_assembly'] = $this->fixed_assembly_no;
        return $data;
    }


    /**
     * 验证信息返回
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
            'content.name.required' => '请输入板块名称',
            'content.name.max' => '板块名称不能超过10个字符',
            'content.url.alias.present' => 'url链接别名参数未设置',
            'content.url.value.present' => '请设置url链接别名值参数',
            'content.width.required' => '请输入图片宽度',
            'content.width.in' => '图片宽度范围是330px',
            'content.height.required' => '请输入图片高度',
            'content.height.numeric' => '高度参数格式不正确',
            'content.height.between' => '图片高度范围是150px~200px',
            'content.data.array' => '板块数据格式不正确',
            'content.data.*.image.required' => '请上传图片',
            'content.data.*.is_show.required' => '请设置内容是否展示',
            'content.data.*.is_show.in' => '板块是否展示参数格式不正确',
            'content.data.*.url.alias.present' => 'url链接别名参数未设置',
            'content.data.*.url.value.present' => '请设置url链接别名值参数',
            'content.data.*.sort.integer' => '排序参数格式不正确',
            'content.data.*.sort.max' => '排序最大值是100',
            'content.data.*.sort.min' => '排序最小值是1',
        ];
    }


    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $source = $data['source'] ?? null;
        $content = $data['content'];
        $mobileRouterService = app(MobileRouterService::class);
        $items = collect($content['data'])->sortByDesc('sort')
            ->filter()
            ->map(function ($item) use (&$items, $mobileRouterService, $source) {
            $item['url_alias'] = $item['url']['alias'] ?? '';
            if (($item['url']['alias'] ?? '')) {
                $urls = $mobileRouterService->handleUrl($item['url']['alias'], $item['url']['value'] ?? '', $source);
            }
            $item['url'] = $urls ?? '';
            return $item;
        })->values()->toArray();
        $urls = '';
        if (($content['url']['alias'] ?? '')) {
            $urls = $mobileRouterService->handleUrl($content['url']['alias'], $content['url']['value'] ?? '', $source);
        }


        // 数据重组
        $new_data = [];
        $new_data['sort'] = $data['sort'] ?? 1;
        $new_data['component_name'] = $data['component_name'];
        $new_data['name'] = $content['name'] ?? '';
        $new_data['url'] = $urls;
        $new_data['url_alias'] = $content['url']['alias'] ?? '';
        $new_data['width'] = $this->width; // 宽度：不支持修改
        $new_data['height'] = $content['height'];
        foreach ($items as $k => $val) {
            if ($val['is_show'] == Constant::ONE) {
                $new_data['items'][] = [
                    'icon' => $val['image'] ?? '',
                    'url' => $val['url'],
                    'url_alias' => $val['url_alias']
                ];
            }
        }
        $new_data['items'] = $new_data['items'] ?? [];
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
        $content['data'] = collect($content['data'] ?? [])->map(function ($item) use ($mobile_router_service) {
            $item['url']['default_selection_data'] = $mobile_router_service->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);
            return $item;
        })->toArray();


        return [
            'id' => $data['id'] ?? 0,// 组件自增id
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? Constant::ONE, // 是否展示 1展示0不展示
            'sort' => $data['sort'] ?? Constant::ONE, // 排序
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? Constant::ZERO, // 是否是固定组件 1是 0否
            'content' => $content ?? [],
            'data' => $this->getContent($data),
        ];
    }
}
