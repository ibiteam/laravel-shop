<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class AdvertisingThreeComponent extends PageComponent
{


	private int $width = 230;
	private int $height = 280;



	public function icon(): array
    {
        return [
            'name' => '广告位3',
            'component_name' => $this->getComponentName(),
            'limit' => 0,
            'icon' => '&#xe7a6;',
            'sort' => '',
        ];
    }


    public function parameter(): array
    {
        return [
            'id' => '',// 组件自增id
            'name' => '广告位3',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE, // 是否展示 1展示0不展示
            'sort' => Constant::ONE, // 排序
            'is_fixed_assembly' => Constant::ZERO, // 是否是固定组件 1是 0否
            'content' => [
                'show_style' => AppWebsiteDecorationItem::AD_SHOW_STYLE_TILE, // 展示样式
                'is_show_title' => Constant::ONE, // 是否显示标题
                'icon' => 'https://cdn.toodudu.com/uploads/2024/01/12/grade@2x.png', // ICON
                'title' => '', // 标题
				'url' => [
					'alias' => 'https', // 链接类型
					'value' => '', // 链接
					'default_selection_data' => [], //选中的数据
				],
                'width' => $this->width,
                'height' => $this->height,
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
            'component_name' => 'required|in:'. AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE,
            'is_show' => 'required|integer|in:' . $is_show_validate_string,
            'content' => 'required|array',
            'content.show_style' => 'required|in:'.AppWebsiteDecorationItem::AD_SHOW_STYLE_TILE.','.AppWebsiteDecorationItem::AD_SHOW_STYLE_CAROUSEL,
            'content.is_show_title' => 'present|nullable|in:'.$is_show_validate_string,
            'content.icon' => 'present|nullable',
            'content.title' => 'present|nullable|required_if:content.is_show_title,'.Constant::ONE.'|max:6',
			'content.url.alias' => 'present|nullable',
			'content.url.value' => 'present|nullable',
            'content.height' => 'required|numeric|between:280,400',
            'content.width' => 'required|numeric|in:'.$this->width,
            'content.data' => 'required|array',
            'content.data.*.url.alias' => 'present|nullable',
            'content.data.*.url.value' => 'present|nullable',
            'content.data.*.image' => 'required',
            'content.data.*.is_show' => 'required|in:' . $is_show_validate_string,
            'content.data.*.sort' => 'nullable|sometimes|integer|min:1|max:100',
        ], $this->messages());

        if ($validator->fails()) {
            throw new BusinessException($this->getName() . '：' . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
		$validate = $validator->validated();


        //校验url链接的正确性
        $mobile_router_service = new MobileRouterService();

		if ($validate['content']['url']['alias'] && $validate['content']['is_show_title']) {
			try {
				$mobile_router_service->viodData($validate['content']['url']['alias'], $validate['content']['url']['value']);
			} catch (\Exception $exception) {
				throw new BusinessException($this->getName() . '：' . $exception->getMessage());
			}
		}
		$validate['content']['data'] = collect($data['content']['data'])->map(function ($item, $key) use ($mobile_router_service) {
			if($item['url']['alias'] && $item['is_show']){
				try {
					$mobile_router_service->viodData($item['url']['alias'], $item['url']['value']);
				} catch (\Exception $exception) {
					throw new BusinessException($this->getName() . '：' . '广告位数据，第'.($key + 1).'条，'.$exception->getMessage());
				}
			}

            $item['sort'] = (int) $item['sort'] ?: 1;
            return $item;
        })->sortByDesc('sort')->values()->toArray();

		return [
			'id' => $validate['id'],
			'name' => $validate['name'],
			'component_name' => $validate['component_name'],
			'is_show' => $validate['is_show'],
			'is_fixed_assembly' => Constant::ZERO,
			'sort' => (int) ($validate['sort'] ?? 0),
			'content' => $validate['content'],
		];
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
            'content.show_style.required' => '请设置展示样式',
            'content.show_style.in' => '展示样式参数格式不正确',
            'content.is_show_title.present' => '请设置标题样式参数',
            'content.is_show_title.in' => '标题样式参数格式不正确',
            'content.icon.present' => '请设置icon参数',
            'content.title.present' => '请设置标题参数',
            'content.title.required_if' => '请输入标题',
            'content.title.max' => '标题不能超过:max个字符',
			'content.url.alias.present' => 'url链接别名参数未设置',
			'content.url.value.present' => '请设置url链接别名值参数',
            'content.height.required' => '请输入图片高度',
            'content.width.required' => '请输入图片宽度',
            'content.width.in' => '图片宽度是230px',
            'content.height.between' => '图片高度范围是280px~400px',
            'content.data.array' => '板块数据格式不正确',
            'content.data.*.is_show.required' => '请设置内容是否展示',
            'content.data.*.is_show.in' => '板块是否展示参数格式不正确',
            'content.data.*.image.required' => '请上传图片',
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
		//重组items
		$items = [];
        collect($content['data'])->where('is_show',Constant::ONE)->map(function ($item) use (&$items, $mobileRouterService, $source) {
            $alias = $item['url']['alias'] ?? '';
            if ($alias) {
                $urls = $mobileRouterService->handleUrl($item['url']['alias'], $item['url']['value'] ?? '', $source);
            }
            if (!(is_harmony_request() && in_array($item['url']['alias'],Router::$harmony_no_show))) {
                $items[] = [
                    'icon' => $item['image'] ?? '',
                    'url' => $urls??'',
                    'url_alias' => $item['url']['alias'] ?? '',
                ];
            }

            return $item;
        })->sortByDesc('sort')->values()->toArray();

		$url = '';
		if($content['url']['alias']??''){
            if (!(is_harmony_request() && in_array($content['url']['alias'],Router::$harmony_no_show))) {
                $url = $mobileRouterService->handleUrl($content['url']['alias']??'', $content['url']['value'] ?? '', $source);
            }
		}
        return [
			'component_name'=>$data['component_name']??'',
			'sort'=>$data['sort']??0,
			'show_style'=>$content['show_style']??AppWebsiteDecorationItem::AD_SHOW_STYLE_TILE,
			'is_show_title'=>$content['is_show_title']??Constant::ZERO,
			'icon'=>$content['icon']??'',
			'title'=>$content['title']??'',
			'url'=>$url,
			'url_alias'=>$content['url']['alias']??'',
			'width'=>$this->width, // 宽度：不支持修改
			'height'=>$content['height']??$this->height,
			'items'=>$items,
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
        $mobile_router_service = new MobileRouterService();
        $content['data'] = collect($content['data'] ?? [])->map(function ($item) use ($mobile_router_service) {
            $item['url']['default_selection_data'] = $mobile_router_service->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);
            return $item;
        })->toArray();

		$content['show_style'] = $content['show_style']??AppWebsiteDecorationItem::AD_SHOW_STYLE_TILE;
		$content['is_show_title'] = $content['is_show_title']??Constant::ZERO;
		$content['icon'] = $content['icon']??'';
		$content['title'] = $content['title']??'';
		$content['url'] = [
			'alias' => $content['url']['alias']??'',
			'value' => $content['url']['value']??'',
			'default_selection_data' => $mobile_router_service->getOption($content['url']['alias'] ?? '', $content['url']['value'] ?? '', true),
		];

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
