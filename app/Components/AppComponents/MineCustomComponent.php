<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Models\SellerShopinfo;
use App\Models\ShopConfig;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

/**
 * 我的-自定义板块.
 */
class MineCustomComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => '自定义板块',
            'component_name' => $this->getComponentName(),
            'limit' => -1,
            'icon' => '&#xe7d9;',
            'sort' => '',
        ];
    }

    /**
     * init left and right form data.
     * {@inheritDoc}
     */
    public function parameter(): array
    {
        return [
            'id' => '',
            'name' => '自定义板块',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => Constant::ZERO,
            'content' => [
                'name' => '',  //Plate type
                'style' => AppWebsiteDecorationItem::MINE_CUSTOM_STYLE_TWO, //Rotation style  1横向滚动 2整屏翻页
                'plate_height' => 3, //Plate height
                'number_row' => 5, //Mumber of row
                'menu' => [ //Menu data
                    [
                        'image' => '', //picture
                        'gif' => '',  //git
                        'name' => '', //name
                        'url' => [ //URL link
                            'alias' => 'https',
                            'value' => '',
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => '', //sort
                        'app_show' => 1, //displayed on the app side
                        'h5_show' => 1, //displayed on the h5 side
                        'mini_show' => 1, //displayed on the mini side
                    ],
                ],
            ],
        ];
    }

    /**
     * obtain display data
     * {@inheritDoc}
     */
    public function getContent(array $data): array
    {
        $user = $data['user'] ?? null;
        $source = $data['source'] ?? '';
        $content = $data['content'] ?? [];
		$is_admin = false;
		if (MobileRouterService::SOURCE_MINI == $source) { //小程序
            $collect_menus = collect($content['menu'] ?? [])->where('mini_show', 1);
        } elseif (MobileRouterService::SOURCE_H5 == $source) {
            $collect_menus = collect($content['menu'] ?? [])->where('h5_show', 1);
        } elseif (MobileRouterService::SOURCE_APP == $source) {
            $collect_menus = collect($content['menu'] ?? [])->where('app_show', 1);
        } else {
			$is_admin = true; 	//总后台
			$collect_menus = collect($content['menu'] ?? [])->where('app_show', 1);
        }
        $menus = $collect_menus->sortByDesc('sort')->toArray();
        $temp_menu = [];
        $mobileRouterService = app(MobileRouterService::class);
        foreach ($menus as $menu) {
            $alias = $menu['url']['alias'] ?? '';
            $alias_value = $menu['url']['value'] ?? '';
            if ($alias) {
				//当前用户不符合展示直播条件则跳过不存入menu
				if (!$is_admin && Router::LIVE_MANAGE == $alias) { //直播
					if($source != MobileRouterService::SOURCE_APP){
						continue;
					}

					$seller_id = $user->seller_id ?? 0;
					if (!$seller_id || !shop_config(ShopConfig::IS_SHOW_LIVE_BUTTON)) {
						continue;
					}
					if (!SellerShopinfo::whereSellerId($seller_id)->whereStatus(SellerShopinfo::STATUS_OPEN)->exists()) {
						continue;
					}
				}
                if(in_array($alias,Router::$harmony_no_show) && is_harmony_request()){
                    continue;
                }
				$url = $mobileRouterService->handleUrl($alias, $alias_value, $source);
                $temp_menu[] = [
                    'image' => $menu['image'] ?? '',
                    'gif' => $menu['gif'] ?? '',
                    'name' => $menu['name'] ?? '',
                    'alias' => $menu['url']['alias'] ?? '',
                    'url' => $url ?? '',
                ];
            }
        }
        //根据高度和行数处理菜单结构
        $style = $content['style'] ?? '';
        $plate_height = $content['plate_height'] ?? 3;
        $number_row = $content['number_row'] ?? 5;
        if (MobileRouterService::SOURCE_APP != $source) {
            if ($plate_height > 0) {
                $first_screen_sum = $plate_height * $number_row;
                if (AppWebsiteDecorationItem::MINE_CUSTOM_STYLE_ONE == $style) { //横向滚动
                    $first_screen_data[] = array_slice($temp_menu, 0, $first_screen_sum);
                    $res = array_chunk(array_slice($temp_menu, $first_screen_sum), $plate_height);
                    $temp_menu = array_merge($first_screen_data, $res);
                } else { //整屏翻页
                    $temp_menu = array_chunk($temp_menu, $first_screen_sum);
                }
            }
        }

        return [
            'component_name' => $this->getComponentName(),
            'name' => $content['name'] ?? '',
            'custom_data' => [
                'style' => $style,
                'plate_height' => $plate_height,
                'number_row' => $number_row,
                'menu' => $temp_menu,
            ],
        ];
    }

    /**
     *  content_data 用来展示画布上的数据
     * {@inheritDoc}
     */
    public function display(array $data = []): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }

        $content = $data['content'] ?? [];
        $mobile_router_service = new MobileRouterService();
        $content['menu'] = collect($content['menu'] ?? [])->map(function ($item) use ($mobile_router_service) {
            $item['url']['default_selection_data'] = $mobile_router_service->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);

            return $item;
        })->toArray();

        return [
            'id' => $data['id'] ?? 0,
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? 1,
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? 0,
            'sort' => $data['sort'] ?? 0,
            'content' => $content,
            'data' => $this->getContent($data),
        ];
    }

    /**
     * verify request parameters
     * {@inheritDoc}
     */
    public function validate(array $data): array
    {
        $is_show_validate_string = Constant::ONE.','.Constant::ZERO;
        $mobile_router_service = new MobileRouterService();
        $validator = Validator::make($data, [
            'id' => 'present|nullable|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_MINE_CUSTOM,
            'is_show' => 'required|in:'.$is_show_validate_string,
            'content' => 'required|array',
            'content.name' => 'present|nullable|max:10',
            'content.style' => 'required|in:'.AppWebsiteDecorationItem::MINE_CUSTOM_STYLE_ONE.','.AppWebsiteDecorationItem::MINE_CUSTOM_STYLE_TWO,
            'content.plate_height' => 'required|in:1,2,3,-1',
            'content.number_row' => 'required|in:3,4,5',
            'content.menu' => 'required|array',
            'content.menu.*.image' => 'required',
            'content.menu.*.gif' => 'present|nullable',
            'content.menu.*.name' => 'required|max:10|distinct',
            'content.menu.*.url.alias' => 'present|nullable',
            'content.menu.*.url.value' => 'present|nullable',
            'content.menu.*.sort' => 'present|nullable|max:100|min:1',
            'content.menu.*.app_show' => 'required|in:'.$is_show_validate_string,
            'content.menu.*.mini_show' => 'required|in:'.$is_show_validate_string,
            'content.menu.*.h5_show' => 'required|in:'.$is_show_validate_string,
        ], $this->message());
        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();
        //校验url链接的正确性
        $menu = collect($validate['content']['menu'])->map(function ($item, $key) use ($mobile_router_service) {
			try {
				$mobile_router_service->viodData($item['url']['alias'] ?? '', $item['url']['value'] ?? '');
			} catch (\Exception $exception) {
				throw new BusinessException($this->getName().'：菜单数据，第'.($key + 1).'条，'.$exception->getMessage());
			}
            $item['sort'] = (int) $item['sort'] ?: 1;

            return $item;
        })->sortByDesc('sort')->values()->toArray();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => (int) ($data['sort'] ?? 0),
            'content' => [
                'name' => $validate['content']['name'],  //Plate type
                'style' => $validate['content']['style'], //Rotation style  1横向滚动 2整屏翻页
                'plate_height' => $validate['content']['plate_height'], //Plate height
                'number_row' => $validate['content']['number_row'], //Mumber of row
                'menu' => $menu,
            ],
        ];
    }

    private function message(): array
    {
        return [
            'id.present' => '板块ID 未设置',
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID不存在，请刷新重试',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过:max 个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => '板块数据结构不正确',
            'content.name.present' => '板块名称未设置',
            'content.name.max' => '板块名称不能超过:max个字符',
            'content.style.required' => '请设置轮播样式',
            'content.style.in' => '轮播样式参数格式不正确',
            'content.plate_height.required' => '请选择板块高度',
            'content.plate_height.in' => '板块高度参数格式不正确',
            'content.number_row.required' => '请选择每行个数',
            'content.number_row.in' => '每行个数参数格式不正确',
            'content.menu.required' => '请设置菜单数据',
            'content.menu.array' => '菜单数据参数格式不正确',
            'content.menu.*.image.required' => '请上传图片',
            'content.menu.*.gif.present' => '请设置动图参数',
            'content.menu.*.name.required' => '请输入菜单名称',
            'content.menu.*.name.max' => '菜单名称不能超过:max个字符',
            'content.menu.*.name.distinct' => '菜单名称已存在，请修改！',
            'content.menu.*.url.alias.present' => 'url链接别名参数未设置',
            'content.menu.*.url.value.present' => '请设置url链接别名值参数',
            'content.menu.*.sort.present' => '请设置排序参数',
            'content.menu.*.sort.max' => '排序最大值是:max',
            'content.menu.*.sort.min' => '排序最小值是:min',
            'content.menu.*.app_show.required' => '请设置显示在APP端',
            'content.menu.*.app_show.in' => '显示在APP端参数格式不正确',
            'content.menu.*.mini_show.required' => '请设置显示在小程序',
            'content.menu.*.mini_show.in' => '显示在小程序参数格式不正确',
            'content.menu.*.h5_show.required' => '请设置显示在H5端',
            'content.menu.*.h5_show.in' => '显示在H5端参数格式不正确',
        ];
    }
}
