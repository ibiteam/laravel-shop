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

class AdvertisingComponent extends PageComponent
{
    /**
     * 是否是固定组件 1是 0否
     */
    private int $fixed_assembly_no = 0;

    /**
     * 时间类型： 1 长期  0：时间范围
     */
    private int $long_time_yes = 1; // 长期

    /**
     * 广告1 尺寸
     */
    private int $one_width = 710;
    private int $one_height = 200;


    /**
     * 广告2 尺寸
     */
    private int $two_width = 350;
    private int $two_height = 190;


    /**
     * 广告3 尺寸
     */
    private int $three_width = 230;
    private int $three_height = 280;


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


    public function parameter(): array
    {
        $publicData = $this->getPublicData();
        switch ($publicData['component_name']) {
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE :
                return $this->oneParameter($publicData);
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO :
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE :
                return $this->otherParameter($publicData);
            default:
                return [];
        }
    }


    /**
     * 广告1默认数据
     */
    private function oneParameter($publicData) {
        return [
            'id' => '',// 组件自增id
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_no, // 是否是固定组件 1是 0否
            'content' => [
                'width' => $publicData['width'], // 宽度：不支持修改
                'height' => $publicData['height'],
                'data' => [
                    [
                        'image' => '', //图片地址
                        'url' => [
                            'alias' => 'https', // 链接类型
                            'value' => '', // 链接
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => Constant::ONE, // 排序 （1~100）
                        'is_show' => Constant::ONE, // 是否显示 1展示 0隐藏
                        'is_ad' => Constant::ZERO, // 是否是广告 1展示 0隐藏
                        'date' => [
                            'type' => $this->long_time_yes, // 时间类型： 1 长期  0：时间范围
                            'value' => [
                                'start_time' => '',
                                'end_time' => '',
                            ],
                        ],

                    ]
                ],
            ],
        ];
    }


    /**
     * 广告2,3默认数据
     */
    private function otherParameter($publicData) {
       return [
            'id' => '',// 组件自增id
            'name' => $publicData['name'],
            'component_name' => $publicData['component_name'],
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => $this->fixed_assembly_no, // 是否是固定组件 1是 0否
            'content' => [
                'width' => $publicData['width'], // 宽度：不支持修改
                'height' => $publicData['height'],
                'data' => [
                    [
                        'image' => '', //图片地址
                        'url' => [
                            'alias' => 'https', // 链接类型
                            'value' => '', // 链接
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => Constant::ONE, // 排序 （1~100）
                        'is_show' => Constant::ONE // 是否显示 1展示 0隐藏
                    ]
                ],
            ],
        ];
    }


    private function getPublicData()
    {
        // 获取广告1、2、3 尺寸的默认数据
        $component_name = $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE;
        switch ($component_name) {
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE:
                return [
                    'component_name' => $component_name,
                    'width' => $this->one_width,
                    'height' => $this->one_height,
                    'name' => '广告位1',
                    'icon' => '&#xe7a6;',
                    'height_range' => 'required|numeric|between:200,350',
                    'width_range' => 'required|numeric|in:710'
                ];
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO:
                return [
                    'component_name' => $component_name,
                    'width' => $this->two_width,
                    'height' => $this->two_height,
                    'name' => '广告位2',
                    'icon' => '&#xe7a6;',
                    'height_range' => 'required|numeric|between:190,250',
                    'width_range' => 'required|numeric|in:350'
                ];
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE:
                return [
                    'component_name' => $component_name,
                    'width' => $this->three_width,
                    'height' => $this->three_height,
                    'name' => '广告位3',
                    'icon' => '&#xe7a6;',
                    'height_range' => 'required|numeric|between:280,400',
                    'width_range' => 'required|numeric|in:230'
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
        $is_show_validate_string = Constant::ONE . ',' . Constant::ZERO;
        $component_name_string = AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE . ',' . AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO . ',' . AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE;
        $publicData = $this->getPublicData();

        $start_time = 'sometimes';
        $end_time = 'sometimes';
        $required_date = 'nullable';
        $required_type = 'nullable';
        $validate_data = [];
        if ($publicData['component_name'] == AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE) {
            if (isset($data['content']['data']['date']['type']) && $data['content']['data']['date']['type'] == Constant::ZERO) {
                $start_time = 'required_without::end_time';
                $end_time = 'required_without::start_time';
            }
            $required_type = 'required|integer|in:' . $is_show_validate_string;
            $required_date = 'array';
            $validate_data+= ['content.data.*.is_ad'=>'required|integer|in:' . $is_show_validate_string];
        }


        $validate_data += [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:' . $component_name_string,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.height' => $publicData['height_range'],
            'content.width' => $publicData['width_range'],
            'content.data' => 'required|array',
            'content.data.*.url.alias' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            'content.data.*.image' => 'required',
            'content.data.*.is_show' => 'required|in:' . $is_show_validate_string,
            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
            'content.data.*.date' => $required_date,
            'content.data.*.date.type' => $required_type,
            'content.data.*.date.value.start_time' => $start_time,
            'content.data.*.date.value.end_time' => $end_time,
        ];
        $validator = Validator::make($data, $validate_data, $this->messages());

        if ($validator->fails()) {
            throw new BusinessException($publicData['name'] . '：' . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();


        //校验url链接的正确性
        $mobile_router_service = new MobileRouterService();
        $data['content']['data'] = collect($data['content']['data'])->map(function ($item, $key) use ($mobile_router_service, $publicData) {
			if($item['is_show']){
				try {
					$mobile_router_service->viodData($item['url']['alias'] ?? '', $item['url']['value'] ?? '');
				} catch (\Exception $exception) {
					throw new BusinessException($publicData['name'] . '：' . '菜单数据，第'.($key + 1).'条，'.$exception->getMessage());
				}
			}

            $item['sort'] = (int) $item['sort'] ?: 1;
            return $item;
        })->sortByDesc('sort')->values()->toArray();

        $data['component_name'] = $publicData['component_name'];
        $data['is_fixed_assembly'] = $this->fixed_assembly_no;
        return $data;
    }


    /**
     * 验证信息返回
     */
    private function messages(): array
    {
        // 获取广告1、2、3 尺寸的默认宽高
        $height_messages = '';
        $component_name = $this->getComponentName() ?: AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE;
        switch ($component_name) {
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE:
                $height_messages = '图片高度范围是200px~350px';
                $width_messages = '图片宽度是710px';
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO:
                $height_messages = '图片高度范围是190px~250px';
                $width_messages = '图片宽度是350px';
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE:
                $height_messages = '图片高度范围是280px~400px';
                $width_messages = '图片宽度是230px';
                break;
        }

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
            'content.height.required' => '请输入图片高度',
            'content.width.required' => '请输入图片宽度',
            'content.width.in' => $width_messages,
            'content.height.between' => $height_messages,
            'content.data.array' => '板块数据格式不正确',
            'content.data.*.is_show.required' => '请设置内容是否展示',
            'content.data.*.is_show.in' => '板块是否展示参数格式不正确',
            'content.data.*.is_ad.required' => '请设置是否展示广告标识',
            'content.data.*.is_ad.in' => '广告是否展示参数格式不正确',
            'content.data.*.image.required' => '请上传图片',
            'content.data.*.url.alias.present' => 'url链接别名参数未设置',
            'content.data.*.url.value.present' => '请设置url链接别名值参数',
            'content.data.*.sort.integer' => '排序参数格式不正确',
            'content.data.*.sort.max' => '排序最大值是100',
            'content.data.*.sort.min' => '排序最小值是1',
            'content.data.*.date.array' => '板块数据格式不正确',
            'content.data.*.date.type.required' => '请选择时间期限',
            'content.data.*.date.value.start_time.required_without' => '请选择显示时间',
            'content.data.*.date.value.end_time.required_without' => '请选择显示时间',
        ];
    }


    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $publicData = $this->getPublicData();
        $source = $data['source'] ?? null;
        $content = $data['content'];
        $mobileRouterService = app(MobileRouterService::class);
        $server_is_show = shop_config(ShopConfig::SERVER_IS_SHOW);
        $items = collect($content['data'])->sortByDesc('sort')
            ->filter(function ($item) use ($server_is_show) {
            if ($item['url']['alias'] === Router::CUSTOMER_SERVICE && !$server_is_show) {
                return false;
            }
            if (is_harmony_request() && in_array($item['url']['alias'],Router::$harmony_no_show)) {
                return false;
            }
            return true;
        })->map(function ($item) use (&$items, $mobileRouterService, $source) {
            $item['url_alias'] = $item['url']['alias'] ?? '';
            if (($item['url']['alias'] ?? '')) {
                $urls = $mobileRouterService->handleUrl($item['url']['alias'], $item['url']['value'] ?? '', $source);
            }
            $item['url'] = $urls ?? '';
            $item['is_ad'] = ($item['is_ad']??'')?1:0;

            return $item;
        })->values()->toArray();


        // 数据重组
        $new_data = [];
        switch ($data['component_name']) {
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE :
                $new_data = $this->advertisementOne($data, $publicData, $content, $items);
                return $new_data;
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO :
                $new_data = $this->otherAdvertisement($data, $publicData, $content, $items);
                return $new_data;
            case AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE :
                $new_data = $this->otherAdvertisement($data, $publicData, $content, $items);
                return $new_data;
            default:
                return $new_data;
        }
    }


    /**
     * 广告1数据返回
     */
    private function advertisementOne($data, $publicData, $content, $items) {
        $new_data['component_name'] = $data['component_name'];
        $new_data['width'] = $publicData['width']; // 宽度：不支持修改
        $new_data['height'] = $content['height'];
        $new_data['sort'] = $data['sort'] ?? 1;
        foreach ($items as $k => $val) {
            if ($val['is_show'] == Constant::ONE) {
                $now_time = date('Y-m-d H:i:s');
                if (isset($val['date']['type'])) {
                    if (
                        ($val['date']['type'] == Constant::ONE)
                        || (
                            ($val['date']['type'] == Constant::ZERO)
                            && ($now_time >= $val['date']['value']['start_time'])
                            && ($now_time <= $val['date']['value']['end_time'])
                        )
                    ) {
                        $new_data['items'][] = [
                            'icon' => $val['image'] ?? '',
                            'url' => $val['url'],
                            'is_ad' => ($val['is_ad']??'')?true:false,
                            'url_alias' => $val['url_alias']
                        ];
                    }
                } else {
                    $new_data['items'][] = [
                        'icon' => $val['image'] ?? '',
                        'url' => $val['url'],
                        'is_ad' => ($val['is_ad']??'')?true:false,
                        'url_alias' => $val['url_alias']
                    ];
                }


            }
        }
        $new_data['items'] = $new_data['items'] ?? [];
        return $new_data;
    }


    /**
     * 广告2数据，广告3数据返回
     */
    private function otherAdvertisement($data, $publicData, $content, $items) {
        $new_data['component_name'] = $data['component_name'];
        $new_data['width'] = $publicData['width']; // 宽度：不支持修改
        $new_data['height'] = $content['height'];
        $new_data['sort'] = $data['sort'] ?? 1;
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
        $content['data'] = collect($content['data'] ?? [])->map(function ($item) use ($mobile_router_service,$data) {
            $item['url']['default_selection_data'] = $mobile_router_service->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);
            if ($data['component_name'] == AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE){
                $item['is_ad'] = ($item['is_ad']??'')?Constant::ONE:Constant::ZERO;
            }
            return $item;
        })->toArray();

        if ($data['component_name'] == AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE && $content['data']) {
            foreach ($content['data'] as $k => $val) {
                if (!isset($val['date'])) {
                    $content['data'][$k]['date'] = [
                        'type' => $this->long_time_yes,
                        'value' => [
                            'start_time' => '',
                            'end_time' => ''
                        ]
                    ];
                }
            }
        }


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
