<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\NotifyItemDao;
use App\Models\AppWebsiteDecoration;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Models\ShopConfig;
use App\Models\User;
use App\Services\MobileRouterService;
use App\Services\RomoteSearchService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class HomeNavColumnComponent extends PageComponent
{
    private const HOME_FIXED_POSITION = 'left';
    private const CATEGORY_FIXED_POSITION = 'right';

    /**
     * {@inheritDoc}
     */
    public function icon(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function parameter(): array
    {
        $home_website_decoration_id = '';
        $home_website_decoration = AppWebsiteDecoration::query()->whereAlias(AppWebsiteDecoration::ALIAS_HOME)->first();
        if ($home_website_decoration) {
            $home_website_decoration_id = $home_website_decoration->id;
        }
        $category_website_decoration_id = '';
        $category_website_decoration = AppWebsiteDecoration::query()->whereAlias(AppWebsiteDecoration::ALIAS_CATEGORY)->first();
        if ($category_website_decoration) {
            $category_website_decoration_id = $category_website_decoration->id;
        }

        return [
            'id' => '',
            'name' => '导航栏',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ONE,
            'sort' => Constant::ZERO,
            'content' => [
                'base_data' => [
                    'logo' => shop_config(ShopConfig::SHOP_LOGO),
                    'top_bg_image' => 'https://cdn.toodudu.com/uploads/2023/10/27/top_nav_bg_image.png',
                    'top_default_bg_image' => 'https://cdn.toodudu.com/uploads/2023/10/27/top_nav_bg_image.png',
                    'home_bg_image' => 'https://cdn.toodudu.com/uploads/2023/10/27/home_bg_image.png',
                    'home_default_bg_image' => 'https://cdn.toodudu.com/uploads/2023/10/27/home_bg_image.png',
                    'notice' => [
                        'is_show' => Constant::ONE,
                        'icon' => 'https://cdn.toodudu.com/uploads/2023/10/27/notice.png',
                        'default_icon' => 'https://cdn.toodudu.com/uploads/2023/10/27/notice.png',
                        'font_color' => '#3D3D3D',
                    ],
                    'sign_in' => [
                        'is_show' => Constant::ONE,
                        'icon' => 'https://cdn.toodudu.com/uploads/2023/10/27/sign_in.png',
                        'default_icon' => 'https://cdn.toodudu.com/uploads/2023/10/27/sign_in.png',
                        'font_color' => '#3D3D3D',
                    ],
                ],
                'search_data' => [
                    'button_color' => '#F71111',
                    'search_font_color' => '#FFFFFF',
                    'items' => [
                        ['keywords' => '', 'sort' => '1', 'value' => '', 'type' => 'https'],
                    ],
                ],
                'nav_data' => [
                    'font_default_color' => '#333333',
                    'font_selection_color' => '#333333',
                    'items' => [
                        ['title' => '推荐', 'sort' => '11', 'icon' => '', 'value' => $home_website_decoration_id, 'fixed_position' => self::HOME_FIXED_POSITION],
                        ['title' => '分类', 'sort' => '1', 'icon' => 'https://cdn.toodudu.com/uploads/2023/11/15/home_category_bg.png', 'value' => $category_website_decoration_id, 'fixed_position' => self::CATEGORY_FIXED_POSITION],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(array $data): array
    {
        $content = $data['content'];
        $source = $data['source'] ?? MobileRouterService::SOURCE_APP;
        $base_data = $content['base_data'];
        $search_data = $content['search_data'];
        $nav_data = $content['nav_data'];

        $result = [
            'component_name' => $this->getComponentName(),
            'sort' => $data['sort']??0,
            'base_data' => [
                'logo' => $base_data['logo'],
                'top_bg_image' => $base_data['top_bg_image'],
                'home_bg_image' => $base_data['home_bg_image'],
                'items' => [],
            ],
            'search_data' => [
                'button_color' => $search_data['button_color'],
                'search_font_color' => $search_data['search_font_color'],
                'items' => [],
            ],
            'nav_data' => [
                'font_default_color' => $nav_data['font_default_color'],
                'font_selection_color' => $nav_data['font_selection_color'],
                'items' => [],
            ],
            'is_show_nav_data' => false,
        ];
        $app = app(MobileRouterService::class);
        $notice = $base_data['notice'];
        if (Constant::ONE == ($notice['is_show'] ?? Constant::ZERO) && $source === MobileRouterService::SOURCE_APP && !is_harmony_request()) {
            $user = $data['user'] ?? null;
            $not_read_data = $user instanceof User ? app(NotifyItemDao::class)->getNotifyNotReadNum($user->user_id, $user->reg_time ?? time()) : ['not_read_count' => 0];
            $not_read_number = $not_read_data['not_read_count'] ?? 0;
            $is_show_number = Constant::ONE;
            if ($not_read_number > 99) {
                $not_read_number = '···';
            } else {
                if (!$not_read_number) {
                    $is_show_number = Constant::ZERO;
                }
            }
            $result['base_data']['items'][] = [
                'icon' => $notice['icon'],
                'text' => '消息',
                'font_color' => $notice['font_color'],
                'is_show_number' => $is_show_number,
                'not_read_number' => $is_show_number == Constant::ONE ? (string)$not_read_number : '',
                'url' => $app->handleUrl(Router::NO_HOME_APP_MESSAGE, '', $source)
            ];
        }
        $sign_in = $base_data['sign_in'];
        if (Constant::ONE == ($sign_in['is_show'] ?? Constant::ZERO) && !is_harmony_request()) {
            $result['base_data']['items'][] = [
                'icon' => $sign_in['icon'],
                'text' => '签到',
                'font_color' => $sign_in['font_color'],
                'is_show_number' => Constant::ZERO,
                'not_read_number' => '',
                'url' => $app->handleUrl(Router::SIGNIN, '', $source)
            ];
        }
        /* 搜索处理 */
        $server_is_show = shop_config(ShopConfig::SERVER_IS_SHOW);
        $temp_search_data = [];
        $search_items = collect($search_data['items'])->sortByDesc('sort')->values()->toArray();
        foreach ($search_items as $search_datum) {
            if ($search_datum['type'] === Router::CUSTOMER_SERVICE && !$server_is_show) {
                continue;
            }
            $temp_search_data[] = [
                'keywords' => $search_datum['keywords'],
                'sort' => $search_datum['sort'],
                'url' => $app->handleUrl($search_datum['type'], $search_datum['value'], $source),
                'url_alias' => $search_datum['type']
            ];
        }
        $result['search_data']['items'] = collect($temp_search_data)->sortByDesc('sort')->values()->toArray();

        /* 导航处理 */
        $temp_data = [];
        $nav_items = collect($nav_data['items'])->sortByDesc('sort')->values()->toArray();
        $app_website_decorations = AppWebsiteDecoration::query()->whereIn('id', array_column($nav_items, 'value'))->select('id', 'name')->get();
        $temp_app = app(MobileRouterService::class);
        $left_data = [];
        $right_data = [];
        foreach ($nav_items as $nav_item) {
            $app_website_decoration = $app_website_decorations->where('id', $nav_item['value'])->first();
            if (!$app_website_decoration) {
                continue;
            }
            if (self::HOME_FIXED_POSITION === $nav_item['fixed_position']) {
                $url = $temp_app->handleUrl(Router::HOME, $app_website_decoration->id, $source);
                $left_data = [
                    'index' => $app_website_decoration->id,
                    'title' => $nav_item['title'],
                    'fixed_position' => $nav_item['fixed_position'],
                    'sort' => (int) $nav_item['sort'],
                    'url' => $url,
                    'icon' => $nav_item['icon'],
                ];
            } else if (self::CATEGORY_FIXED_POSITION === $nav_item['fixed_position']) {
                $url = $temp_app->handleUrl(Router::NO_HOME_CATEGORY, $app_website_decoration->id, $source);
                $right_data = [
                    'index' => $app_website_decoration->id,
                    'title' => $nav_item['title'],
                    'fixed_position' => $nav_item['fixed_position'],
                    'sort' => (int) $nav_item['sort'],
                    'url' => $url,
                    'icon' => $nav_item['icon'],
                ];
            } else {
                $url = $temp_app->handleUrl(Router::INDUSTRY, $app_website_decoration->id, $source);
                $temp_data[] = [
                    'index' => $app_website_decoration->id,
                    'title' => $nav_item['title'],
                    'fixed_position' => $nav_item['fixed_position'],
                    'sort' => (int) $nav_item['sort'],
                    'url' => $url,
                    'icon' => $nav_item['icon'],
                ];
            }
        }
        $temp_nav_item_data = collect($temp_data)->sortByDesc('sort');
        if (!empty($left_data)) {
            $temp_nav_item_data->prepend($left_data);
        }
        if (!empty($right_data)) {
            $temp_nav_item_data->add($right_data);
        }
        $temp_sort_item_data = $temp_nav_item_data->values()->toArray();
        if (count($temp_sort_item_data) >= 3) {
            $result['is_show_nav_data'] = true;
            $result['nav_data']['items'] = $temp_sort_item_data;
        } else {
            $result['nav_data'] = null;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function display(array $data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];
        $mobile_router_service = new MobileRouterService();

        $content['search_data']['items'] = collect($content['search_data']['items'] ?? [])->map(function ($item) use ($mobile_router_service) {
            return [
                'keywords' => $item['keywords'],
                'sort' => $item['sort'] ?? '',
                'value' => $item['value'],
                'type' => $item['type'],
                'default_selection_data' => $mobile_router_service->getOption($item['type'], $item['value'], true),
            ];
        })->toArray();

        $content['nav_data']['items'] = collect($content['nav_data']['items'] ?? [])->map(function ($item) {
            $default_selection_data = [];
            $temp_app_website_decoration = AppWebsiteDecoration::query()->whereId($item['value'])->first();
            if ($temp_app_website_decoration) {
                switch ($item['fixed_position'] ?? '') {
                    case self::HOME_FIXED_POSITION:
                    case self::CATEGORY_FIXED_POSITION:
                        $default_selection_data = [['label' => "【{$temp_app_website_decoration->id}】{$temp_app_website_decoration->name}",'value' => $temp_app_website_decoration->id]];
                        break;
                    default:
                        $default_selection_data = (new RomoteSearchService())->getOption(RomoteSearchService::TYPE_APP_WEBSITE_INDUSTRIAL,$item['value'],true);
                }
            }

            return [
                'title' => $item['title'],
                'sort' => $item['sort'] ?? '',
                'value' => $item['value'],
                'icon' => $item['icon'],
                'fixed_position' => $item['fixed_position'] ?? '',
                'default_selection_data' => $default_selection_data,
            ];
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
     * 表单验证
     *
     * @throws BusinessException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data): array
    {
        $is_show_validate_string = Constant::ONE.','.Constant::ZERO;
        $validator = Validator::make($data, [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'sort' => 'nullable',
            'content' => 'required|array',
            /* 基本信息 */
            'content.base_data' => 'required|array',
            'content.base_data.logo' => 'required|url',
            'content.base_data.top_bg_image' => 'required|url',
            'content.base_data.top_default_bg_image' => 'nullable',
            'content.base_data.home_bg_image' => 'required|url',
            'content.base_data.home_default_bg_image' => 'nullable',
            'content.base_data.notice' => 'required|array',
            'content.base_data.notice.is_show' => 'required|in:'.$is_show_validate_string,
            'content.base_data.notice.icon' => 'required|url',
            'content.base_data.notice.default_icon' => 'nullable',
            'content.base_data.notice.font_color' => 'required',
            'content.base_data.sign_in' => 'required|array',
            'content.base_data.sign_in.is_show' => 'required|in:'.$is_show_validate_string,
            'content.base_data.sign_in.icon' => 'required|url',
            'content.base_data.sign_in.default_icon' => 'nullable',
            'content.base_data.sign_in.font_color' => 'required',
            /* 搜索信息 */
            'content.search_data' => 'required|array',
            'content.search_data.button_color' => 'required',
            'content.search_data.search_font_color' => 'required',
            'content.search_data.items' => 'required|array|max:10',
            'content.search_data.items.*.keywords' => 'required|distinct|max:15',
            'content.search_data.items.*.sort' => 'required|integer|min:1|max:100',
            'content.search_data.items.*.value' => 'present',
            'content.search_data.items.*.type' => 'required',
            /* 导航页面 */
            'content.nav_data' => 'required|array',
            'content.nav_data.font_default_color' => 'required',
            'content.nav_data.font_selection_color' => 'required',
            'content.nav_data.items' => 'required|array|max:10',
            'content.nav_data.items.*.title' => 'required|max:5',
            'content.nav_data.items.*.sort' => 'required|integer|min:1|max:100',
            'content.nav_data.items.*.value' => [
                'required',
                'distinct',
                function ($attribute, $value, $fail) use ($data) {
                    $index = str_replace('content.nav_data.items.', '', str_replace('.value', '', $attribute));
                    $fixed_position = $data['content']['nav_data']['items'][$index]['fixed_position'] ?? '';
                    switch ($fixed_position) {
                        case self::HOME_FIXED_POSITION:
                            if (!(AppWebsiteDecoration::query()->whereId($value)->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereIsShow(Constant::ONE)->whereAlias(AppWebsiteDecoration::ALIAS_HOME)->exists())) {
                                $fail('关联产业链页面不存在或未展示，请调整后重试');
                            }

                            break;
                        case self::CATEGORY_FIXED_POSITION:
                            if (!(AppWebsiteDecoration::query()->whereId($value)->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereIsShow(Constant::ONE)->whereAlias(AppWebsiteDecoration::ALIAS_CATEGORY)->exists())) {
                                $fail('关联产业链页面不存在或未展示，请调整后重试');
                            }

                            break;
                        default:
                            if (!AppWebsiteDecoration::query()->whereId($value)->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->whereIsShow(Constant::ONE)->whereAlias(AppWebsiteDecoration::ALIAS_INDUSTRIAL)->where('parent_id', '!=', Constant::ZERO)->exists()) {
                                $fail('关联产业链页面不存在或未展示，请调整后重试');
                            }
                    }
                },
            ],
            'content.nav_data.items.*.fixed_position' => 'present|nullable|distinct',
            'content.nav_data.items.*.icon' => 'required_if:content.nav_data.items.*.fixed_position,'.self::CATEGORY_FIXED_POSITION.'|nullable|url',
        ], [
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID 不正确，请刷新页面后重试',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过:max个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => '板块对应数据格式不正确',
            /* 基本信息 */
            'content.base_data.required' => '板块对应基本信息未填写，请填写后重新提交',
            'content.base_data.array' => '板块对应基本信息格式不正确',
            'content.base_data.logo.required' => '请上传LOGO',
            'content.base_data.logo.url' => 'logo 格式不正确',
            'content.base_data.top_bg_image.required' => '请上传导航背景图',
            'content.base_data.top_bg_image.url' => '导航背景图格式不正确',
            'content.base_data.home_bg_image.required' => '请上传首页背景图',
            'content.base_data.home_bg_image.url' => '首页背景图格式不正确',
            'content.base_data.notice.required' => '签到信息未设置',
            'content.base_data.notice.array' => '签到信息格式不正确',
            'content.base_data.notice.is_show.required' => '签到信息未设置是否展示',
            'content.base_data.notice.is_show.in' => '签到信息是否展示设置不正确',
            'content.base_data.notice.icon.required' => '请上传签到ICON',
            'content.base_data.notice.icon.url' => '签到ICON格式不正确',
            'content.base_data.notice.font_color.required' => '请设置签到字体颜色',
            'content.base_data.sign_in.required' => '消息信息未设置',
            'content.base_data.sign_in.array' => '消息信息格式不正确',
            'content.base_data.sign_in.is_show.required' => '消息信息未设置是否展示',
            'content.base_data.sign_in.is_show.in' => '消息信息是否展示设置不正确',
            'content.base_data.sign_in.icon.required' => '请上传消息ICON',
            'content.base_data.sign_in.icon.url' => '消息ICON格式不正确',
            'content.base_data.sign_in.font_color.required' => '请设置消息字体颜色',
            /* 全站搜索 */
            'content.search_data.required' => '板块对应全站搜索未填写，请填写后重新提交',
            'content.search_data.array' => '板块对应全站搜索格式不正确',
            'content.search_data.button_color.required' => '请设置全站搜索按钮色值',
            'content.search_data.search_font_color.required' => '请设置全站搜索文字色值',
            'content.search_data.items.required' => '请设置热搜词',
            'content.search_data.items.array' => '热搜词格式不正确',
            'content.search_data.items.max' => '全站搜索最多支持 :max 个关键词',
            'content.search_data.items.*.keywords.required' => '请输入关键词',
            'content.search_data.items.*.keywords.max' => '关键词不能超过 :max 个字符',
            'content.search_data.items.*.keywords.distinct' => '该关键词已存在，请修改！',
            'content.search_data.items.*.sort.required' => '请填写排序',
            'content.search_data.items.*.sort.integer' => '排序格式不正确',
            'content.search_data.items.*.sort.min' => '排序最小值是 :min',
            'content.search_data.items.*.sort.max' => '排序最大值是 :max',
            'content.search_data.items.*.value.present' => 'URL链接值可以为空，但是必传',
            'content.search_data.items.*.type.required' => '请选择链接类型',
            /* 导航页面 */
            'content.nav_data.required' => '请设置导航页面',
            'content.nav_data.array' => '导航页面格式不正确',
            'content.nav_data.font_default_color.required' => '请设置导航页面字体默认颜色',
            'content.nav_data.font_selection_color.required' => '请设置导航页面字体选中颜色',
            'content.nav_data.items.required' => '请设置导航页面内容',
            'content.nav_data.items.array' => '导航页面内容格式不正确',
            'content.nav_data.items.max' => '导航页面最多支持 :max 个',
            'content.nav_data.items.*.title.required' => '请输入页面名称',
            'content.nav_data.items.*.title.max' => '页面名称不能超过 :max 个字符',
            'content.nav_data.items.*.sort.required' => '请填写排序',
            'content.nav_data.items.*.sort.integer' => '排序格式不正确',
            'content.nav_data.items.*.sort.min' => '排序最小值是 :min',
            'content.nav_data.items.*.sort.max' => '排序最大值是 :max',
            'content.nav_data.items.*.value.required' => '请关联页面',
            'content.nav_data.items.*.value.distinct' => '该关联页面已存在，请修改！',
            'content.nav_data.items.*.value.exists' => '页面不存在或未展示，请调整后重试',
            'content.nav_data.items.*.fixed_position.present' => '固定位置可以为空但是必传',
            'content.nav_data.items.*.fixed_position.distinct' => '固定位置已存在，请刷新页面后重试',
            'content.nav_data.items.*.icon.required_if' => '最后一个页面导航，页面导航图标必传',
            'content.nav_data.items.*.icon.url' => '最后一个页面导航，页面导航图标格式不正确',
        ]);

        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();
        /* check router type is it valid */
        $mobile_router_service = new MobileRouterService();
        collect($validate['content']['search_data']['items'])->map(function ($item, $key) use ($mobile_router_service) {
            try {
                $mobile_router_service->viodData($item['type'] ?? '', $item['value'] ?? '');
            } catch (\Exception $exception) {
                throw new BusinessException($this->getName().' 全站搜索 第'.($key + 1).'个菜单中，'.$exception->getMessage());
            }
        });
        /* 处理排序 */
        $validate['content']['search_data']['items'] = collect($validate['content']['search_data']['items'])->sortByDesc('sort')->values()->toArray();
        $temp_nav_items_collect = collect($validate['content']['nav_data']['items']);
        $temp_nav_items = $temp_nav_items_collect->whereNotIn('fixed_position',[self::HOME_FIXED_POSITION,self::CATEGORY_FIXED_POSITION])->sortByDesc('sort')->values();
        $left_nav_items = $temp_nav_items_collect->where('fixed_position',self::HOME_FIXED_POSITION)->first();
        if (!$left_nav_items) {
            throw new BusinessException('未设置首页导航页面');
        }
        $right_nav_items = $temp_nav_items_collect->where('fixed_position',self::CATEGORY_FIXED_POSITION)->first();
        if (!$right_nav_items) {
            throw new BusinessException('未设置分类导航页面');
        }
        $temp_nav_items->prepend($left_nav_items);
        $temp_nav_items->add($right_nav_items);

        $validate['content']['nav_data']['items'] = $temp_nav_items->values()->toArray();

        return [
            'id' => $validate['id'],
            'name' => $validate['name'],
            'component_name' => $validate['component_name'],
            'is_show' => $validate['is_show'],
            'is_fixed_assembly' => Constant::ONE,
            'content' => $validate['content'],
        ];
    }
}
