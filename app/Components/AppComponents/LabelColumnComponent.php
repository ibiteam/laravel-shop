<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecoration;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Services\MobileRouterService;
use App\Services\RomoteSearchService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * 标签栏组件.
 */
class LabelColumnComponent extends PageComponent
{
    public function icon(): array
    {
        return [];
    }

    /**
     * init left and right form data.
     * {@inheritDoc}
     */
    public function parameter(): array
    {
        $home_website_decoration_id = AppWebsiteDecoration::query()->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereAlias(AppWebsiteDecoration::ALIAS_HOME)->value('id');
        $category_website_decoration_id = AppWebsiteDecoration::query()->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereAlias(AppWebsiteDecoration::ALIAS_CATEGORY)->value('id');
        $distribution_website_decoration_id = AppWebsiteDecoration::query()->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereAlias(AppWebsiteDecoration::ALIAS_DISTRIBUTION)->value('id');
        $cart_website_decoration_id = AppWebsiteDecoration::query()->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereAlias(AppWebsiteDecoration::ALIAS_CART)->value('id');
        $mine_website_decoration_id = AppWebsiteDecoration::query()->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereAlias(AppWebsiteDecoration::ALIAS_MINE)->value('id');

        return [
            'id' => '',
            'name' => '标签栏',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ONE,
            'sort' => Constant::ZERO,
            'content' => [
                'default_data' => [
                    'font_default_color' => '#494949',
                    'font_selection_color' => '#F71111',
                    'items' => [
                        ['default_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/default_mobile_home.png', 'selection_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/selection_mobile_home.png', 'title' => '首页', 'check_title' => '首页', 'value' => $home_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 1, 'is_fixed' => Constant::ONE],
                        ['default_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/default_mobile_category.png', 'selection_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/selection_mobile_category.png', 'title' => '分类', 'check_title' => '分类', 'value' => $category_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 2, 'is_fixed' => Constant::ZERO],
                        ['default_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/default_mobile_distribution.png', 'selection_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/selection_mobile_distribution.png', 'title' => '分销', 'check_title' => '分销', 'value' => $distribution_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 3, 'is_fixed' => Constant::ZERO],
                        ['default_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/default_mobile_cart.png', 'selection_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/selection_mobile_cart.png', 'title' => '购物车', 'check_title' => '购物车', 'value' => $cart_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 4, 'is_fixed' => Constant::ZERO],
                        ['default_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/default_mobile_mine.png', 'selection_image' => 'https://cdn.toodudu.com/uploads/2023/10/31/selection_mobile_mine.png', 'title' => '我的', 'check_title' => '我的', 'value' => $mine_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 5, 'is_fixed' => Constant::ONE],
                    ],
                ],
                'activity_data' => [
                    'is_show' => Constant::ZERO,
                    'start_time' => '',
                    'end_time' => '',
                    'font_default_color' => '',
                    'font_selection_color' => '',
                    'items' => [
                        ['default_image' => '', 'selection_image' => '', 'title' => '首页', 'check_title' => '首页', 'value' => $home_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 1, 'is_fixed' => Constant::ONE],
                        ['default_image' => '', 'selection_image' => '', 'title' => '', 'check_title' => '', 'value' => '', 'is_show' => Constant::ZERO, 'sort' => 2, 'is_fixed' => Constant::ZERO],
                        ['default_image' => '', 'selection_image' => '', 'title' => '', 'check_title' => '', 'value' => '', 'is_show' => Constant::ZERO, 'sort' => 3, 'is_fixed' => Constant::ZERO],
                        ['default_image' => '', 'selection_image' => '', 'title' => '', 'check_title' => '', 'value' => '', 'is_show' => Constant::ZERO, 'sort' => 4, 'is_fixed' => Constant::ZERO],
                        ['default_image' => '', 'selection_image' => '', 'title' => '我的', 'check_title' => '我的', 'value' => $mine_website_decoration_id, 'is_show' => Constant::ONE, 'sort' => 5, 'is_fixed' => Constant::ONE],
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
        $content = $data['content'] ?? [];
        $source = $data['source'] ?? [];
        $is_app_request = isset($data['is_app_request']) ? $data['is_app_request'] : true;
        $activity_data = $content['activity_data'] ?? [];
        if (isset($activity_data['is_show']) && Constant::ONE == $activity_data['is_show']) {
            $temp_time = time();
            if (
                isset($activity_data['start_time'])
                && $activity_data['start_time']
                && strtotime($activity_data['start_time']) <= $temp_time
                && isset($activity_data['end_time'])
                && $activity_data['end_time']
                && strtotime($activity_data['end_time']) > $temp_time
            ) {
                // Use activity tags within the current time range of the activity
                return [
                    'component_name' => $this->getComponentName(),
                    'sort' => $data['sort'] ?? 0,
                    'font_default_color' => $activity_data['font_default_color'] ?? '',
                    'font_selection_color' => $activity_data['font_selection_color'] ?? '',
                    'items' => $this->getLabelData($activity_data['items'] ?? [],$source,$is_app_request),
                ];
            }
        }

        $default_data = $content['default_data'] ?? [];

        return [
            'component_name' => $this->getComponentName(),
            'sort' => $data['sort'] ?? 0,
            'font_default_color' => $default_data['font_default_color'] ?? '',
            'font_selection_color' => $default_data['font_selection_color'] ?? '',
            'items' => $this->getLabelData($default_data['items'] ?? [],$source,$is_app_request),
        ];
    }

    /**
     * display 与 parameter 中的格式一致 只多了一个 content_data 用来展示画布上的数据
     * {@inheritDoc}
     */
    public function display(array $data = []): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];
        $content['default_data']['items'] = collect($content['default_data']['items'] ?? [])->map(function ($item) {
            return $this->getDefaultSelectionData($item);
        })->toArray();
        $content['activity_data']['items'] = collect($content['activity_data']['items'] ?? [])->map(function ($item) {
            return $this->getDefaultSelectionData($item);
        })->toArray();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'sort' => $data['sort'],
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
        $validator = Validator::make($data, [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_LABEL,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required|array',
            /* default tab bar */
            'content.default_data' => 'required|array',
            'content.default_data.font_default_color' => 'required',
            'content.default_data.font_selection_color' => 'required',
            'content.default_data.items' => 'required|array|size:5',
            'content.default_data.items.*.default_image' => 'required_if:content.default_data.items.*.is_show,'.Constant::ONE.'|nullable|url',
            'content.default_data.items.*.selection_image' => 'required_if:content.default_data.items.*.is_show,'.Constant::ONE.'|nullable|url',
            'content.default_data.items.*.title' => 'present|max:4',
            'content.default_data.items.*.check_title' => 'nullable|max:4',
            'content.default_data.items.*.value' => [
                'required_if:content.default_data.items.*.is_show,'.Constant::ONE,
                'nullable',
                'distinct',
                Rule::exists('app_website_decorations', 'id')->where(function ($type_query) {
                    return $type_query->whereJsonContains('type', AppWebsiteDecoration::TYPE_BOTTOM_MENU)->where('version',AppWebsiteDecoration::VERSION_BUYER);
                }),
            ], // 数据校验
            'content.default_data.items.*.is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content.default_data.items.*.sort' => 'required|integer',
            'content.default_data.items.*.is_fixed' => 'required|integer|in:'.$is_show_validate_string,
            /* activity tab bar */
            'content.activity_data' => 'required|array',
            'content.activity_data.is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content.activity_data.start_time' => 'required_if:content.activity_data.is_show,'.Constant::ONE,
            'content.activity_data.end_time' => 'required_if:content.activity_data.is_show,'.Constant::ONE,
            'content.activity_data.font_default_color' => 'required_if:content.activity_data.is_show,'.Constant::ONE,
            'content.activity_data.font_selection_color' => 'required_if:content.activity_data.is_show,'.Constant::ONE,
            'content.activity_data.items' => 'required_if:content.activity_data.is_show,'.Constant::ONE.'|array|size:5',
            'content.activity_data.items.*.default_image' => 'nullable',
            'content.activity_data.items.*.selection_image' => 'nullable',
            'content.activity_data.items.*.title' => 'present|max:4',
            'content.activity_data.items.*.check_title' => 'nullable|max:4',
            'content.activity_data.items.*.value' => 'nullable|distinct', // 数据校验
            'content.activity_data.items.*.is_show' => 'required_if:content.activity_data.is_show,'.Constant::ONE.'|integer|in:'.$is_show_validate_string,
            'content.activity_data.items.*.sort' => 'required_if:content.activity_data.is_show,'.Constant::ONE.'|integer',
            'content.activity_data.items.*.is_fixed' => 'required_if:content.activity_data.is_show,'.Constant::ONE.'|integer|in:'.$is_show_validate_string,
        ],
            [
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID 不正确',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过 :max 个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => '板块对应数据格式不正确',
            /* default tab bar */
            'content.default_data.required' => '未设置板块默认标签栏',
            'content.default_data.array' => '板块默认标签栏参数格式不正确',
            'content.default_data.font_default_color.required' => '板块默认标签栏文字默认颜色未设置',
            'content.default_data.font_selection_color.required' => '板块默认标签栏文字选中颜色未设置',
            'content.default_data.items.required' => '板块默认标签栏标签导航未设置',
            'content.default_data.items.array' => '板块默认标签栏标签导航参数格式不正确',
            'content.default_data.items.size' => '板块默认标签栏标签导航参数个数不正确，个数为:size个',
            'content.default_data.items.*.default_image.required_if' => '板块默认标签栏标签导航默认图未设置',
            'content.default_data.items.*.default_image.url' => '板块默认标签栏标签导航默认图格式不正确',
            'content.default_data.items.*.selection_image.required_if' => '板块默认标签栏标签导航选中图未设置',
            'content.default_data.items.*.selection_image.url' => '板块默认标签栏标签导航选中图格式不正确',
            'content.default_data.items.*.title.present' => '板块默认标签栏标签导航标签名称可以为空但是必传',
            'content.default_data.items.*.title.max' => '标签名称不能超过 :max 个字符',
            'content.default_data.items.*.check_title.max' => '标签选中名称不能超过 :max 个字符',
            'content.default_data.items.*.value.required_if' => '板块默认标签栏标签导航未关联页面',
            'content.default_data.items.*.value.distinct' => '板块活动标签栏标签导航关联页面禁止重复选择',
            'content.default_data.items.*.value.exists' => '板块默认标签栏标签导航关联页面不正确！',
            'content.default_data.items.*.is_show.required' => '板块默认标签栏标签导航是否展示未设置',
            'content.default_data.items.*.is_show.integer' => '板块默认标签栏标签导航是否展示参数格式不正确',
            'content.default_data.items.*.is_show.in' => '板块默认标签栏标签导航是否展示参数格式不正确',
            'content.default_data.items.*.sort.required' => '板块默认标签栏标签导航未设置排序',
            'content.default_data.items.*.sort.integer' => '板块默认标签栏标签导航排序参数格式不正确',
            'content.default_data.items.*.is_fixed.required' => '板块默认标签栏标签导航是否固定',
            'content.default_data.items.*.is_fixed.integer' => '板块默认标签栏标签导航是否固定参数格式不正确',
            /* activity tab bar */
            'content.activity_data.required' => '未设置板块活动标签栏',
            'content.activity_data.array' => '板块活动标签栏参数格式不正确',
            'content.activity_data.is_show.required' => '板块活动标签栏是否展示未设置',
            'content.activity_data.is_show.integer' => '板块活动标签栏是否展示参数格式不正确',
            'content.activity_data.is_show.in' => '板块活动标签栏是否展示参数格式不正确',
            'content.activity_data.start_time.required_if' => '板块活动标签栏开始时间未设置',
            'content.activity_data.end_time.required_if' => '板块活动标签栏结束时间未设置',
            'content.activity_data.font_default_color.required_if' => '板块活动标签栏文字默认颜色未设置',
            'content.activity_data.font_selection_color.required_if' => '板块活动标签栏文字选中颜色未设置',
            'content.activity_data.items.required_if' => '板块活动标签栏标签导航未设置',
            'content.activity_data.items.array' => '板块活动标签栏标签导航参数格式不正确',
            'content.activity_data.items.site' => '板块活动标签栏标签导航参数个数不正确',
            'content.activity_data.items.*.title.present' => '板块活动标签栏标签导航标签名称可以为空但是必传',
            'content.activity_data.items.*.title.max' => '标签名称不能超过 :max 个字符',
            'content.activity_data.items.*.check_title.max' => '标签选中名称不能超过 :max 个字符',
            'content.activity_data.items.*.is_show.required' => '板块活动标签栏标签导航是否展示未设置',
            'content.activity_data.items.*.is_show.integer' => '板块活动标签栏标签导航是否展示参数格式不正确',
            'content.activity_data.items.*.is_show.in' => '板块活动标签栏标签导航是否展示参数格式不正确',
            'content.activity_data.items.*.value.distinct' => '板块活动标签栏标签导航关联页面禁止重复选择',
            'content.activity_data.items.*.sort.required' => '板块活动标签栏标签导航未设置排序',
            'content.activity_data.items.*.sort.integer' => '板块活动标签栏标签导航排序参数格式不正确',
            'content.activity_data.items.*.is_fixed.required' => '板块活动标签栏标签导航是否固定',
            'content.activity_data.items.*.is_fixed.integer' => '板块活动标签栏标签导航是否固定参数格式不正确',
        ]);

        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();
        /* 活动 */
        /* check activity_data items */
        collect($validate['content']['activity_data']['items'])->map(function ($item) use ($data) {
            if ($data['content']['activity_data']['is_show'] && $item['is_show']) {
                if (!isset($item['default_image']) || !$item['default_image']) {
                    throw new BusinessException('板块默认标签栏标签导航默认图未设置');
                }
                if (!isset($item['selection_image']) || !$item['selection_image']) {
                    throw new BusinessException('板块活动标签栏标签导航选中图未设置');
                }
                $temp_value = $item['value'] ?? null;
                if (!$temp_value) {
                    throw new BusinessException('板块活动标签栏标签导航未关联页面');
                }
                if (!AppWebsiteDecoration::query()->whereId($temp_value)->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereJsonContains('type', AppWebsiteDecoration::TYPE_BOTTOM_MENU)->exists()) {
                    throw new BusinessException('板块活动标签栏标签导航关联页面不正确');
                }
            }
        });
        /* custom verification default_data.items < 3 exception */
        $default_show_item_count = collect($validate['content']['default_data']['items'])->where('is_show', Constant::ONE)->count();
        if ($default_show_item_count < 3) {
            throw new BusinessException('标签栏默认标签，标签导航个数不能小于3个');
        }
        /* custom verification activity_data.is_show == 1 and activity_data.items < 3 exception */
        if (Constant::ONE == $validate['content']['activity_data']['is_show']) {
            $default_show_item_count = collect($validate['content']['activity_data']['items'])->where('is_show', Constant::ONE)->count();
            if ($default_show_item_count < 3) {
                throw new BusinessException('标签栏活动标签，标签导航个数不能小于3个');
            }
        }

        return [
            'id' => $validate['id'],
            'name' => $validate['name'],
            'component_name' => $validate['component_name'],
            'is_show' => $validate['is_show'],
            'is_fixed_assembly' => Constant::ONE,
            'sort' => $validate['sort'] ?? '',
            'content' => $validate['content'],
        ];
    }

    /**
     * format item data and process default selected data.
     *
     * @return array
     */
    private function getDefaultSelectionData(array $item)
    {
        $default_selection_data = [];
        $app_website_decoration = AppWebsiteDecoration::query()->whereId($item['value'] ?? 0)->first();
        if ($app_website_decoration) {
            $default_selection_data = (new RomoteSearchService())->getOption(RomoteSearchService::TYPE_APP_WEBSITE_COMMON,$item['value'],true);
        }

        return [
            'default_image' => $item['default_image'],
            'selection_image' => $item['selection_image'],
            'title' => $item['title'],
            'check_title' => array_key_exists('check_title',$item) ? $item['check_title'] : $item['title'],
            'value' => $item['value'],
            'is_show' => $item['is_show'],
            'sort' => $item['sort'],
            'is_fixed' => $item['is_fixed'],
            'default_selection_data' => $default_selection_data,
        ];
    }

    /**
     * Organize the label bar, navigate through labels, and return data.
     *
     * @param $content
     */
    private function getLabelData($content,$source,$is_app_request): array
    {
        $app = app(MobileRouterService::class);

        return collect($content)->filter(function ($info) {
            return $info['is_show'] ?? false;
        })->values()->map(function ($info) use ($app,$source,$is_app_request) {
            $url = '';
            $alias = AppWebsiteDecoration::query()->whereId($info['value'])->value('alias');
            if (!$is_app_request && $alias == AppWebsiteDecoration::ALIAS_NOTICE) {
                return null;
            }
            $front_alias = $alias;
            switch ($alias) {
                case AppWebsiteDecoration::ALIAS_HOME:
                    $router_alias = Router::HOME; // 首页

                    break;
                case AppWebsiteDecoration::ALIAS_INDEX:
                    $router_alias = Router::ZIXUN_INDEX; // 指数
                    $front_alias = 'zixun_index';

                    break;
                case AppWebsiteDecoration::ALIAS_CATEGORY:
                    $router_alias = Router::CATEGORY; // 分类

                    break;
                case AppWebsiteDecoration::ALIAS_DISTRIBUTION:
                    $router_alias = Router::PROMOTE; // 分销
                    $front_alias = 'promote';

                    break;
                case AppWebsiteDecoration::ALIAS_NOTICE:
                    $router_alias = Router::HOME_APP_MESSAGE; // 消息

                    break;
                case AppWebsiteDecoration::ALIAS_CART:
                    $router_alias = Router::CART; // 购物车

                    break;
                case AppWebsiteDecoration::ALIAS_ORDER:
                    $router_alias = Router::MY_ORDER; // 我的订单
                    $front_alias = 'myorder_index';

                    break;
                case AppWebsiteDecoration::ALIAS_MINE:
                    $router_alias = Router::MY; // 我的(个人中心)
                    $front_alias = 'ucenter';

                    break;
                case AppWebsiteDecoration::ALIAS_PUBLIC:
                    $router_alias = Router::PUBLIC_PAGE; // 公共页面合集

                    break;
                case AppWebsiteDecoration::ALIAS_ZIXUN_HOME_PAGE:
                    $router_alias = Router::ZIXUN_APP_HOME; // 资讯首页
                    $front_alias = 'zixun_home';
                    break;
                default:
                    $router_alias = '';
            }
            if ($router_alias) {
                $url = $app->handleUrl($router_alias, $info['value'], $source);
            }

            return [
                'default_image' => $info['default_image'],
                'selection_image' => $info['selection_image'],
                'title' => $info['title'],
                'check_title' => array_key_exists('check_title',$info) ? $info['check_title'] : $info['title'],
                'alias' => $alias,
                'value' => $info['value'],
                'sort' => $info['sort'],
                'url' => $url,
                'front_alias' => $front_alias,
            ];
        })->filter()->values()->toArray();
    }
}
