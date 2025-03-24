<?php

namespace App\Http\Controllers\Manage;

use App\Components\ComponentFactory;
use App\Components\PageDefaultDict;
use App\Exceptions\BusinessException;
use App\Http\Dao\AppWebsiteDecorationItemDao;
use App\Models\AppWebsiteDecoration as AppWebsiteDecorationModel;
use App\Models\AppWebsiteDecorationItem;
use App\Services\MobileRouterService;
use App\Services\RomoteSearchService;
use App\Services\SellerMobileRouterService;
use App\Utils\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB as DBFacade;

/**
 * 移动端网站装修.
 */
class AppWebsiteDecorationController extends BaseController
{
    /**
     * app website decoration index(list).
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|null
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            // 1:底部菜单 2:一级菜单 3:二级菜单.
            $type = $request->input('type');
            $version = $request->input('version');
            $query = AppWebsiteDecorationModel::query()->with('adminUser')->withCount('children')->orderBy('id');
            $query->whereParentId(Constant::ZERO);
            /* add type search */
            $query->when($type > 0, function ($type_query) use ($type) {
                $type_query->whereJsonContains('type', (int) $type);
            });
            /* add version search */
            $query->when($version > 0, function ($version_query) use ($version) {
                $version_query->where('version', $version);
            });
            $data = $query->paginate($request->input('number', 10));
            $data->getCollection()->transform(function (AppWebsiteDecorationModel $app_website_decoration) {
                $type_message = array_map(function ($type_value) {
                    switch ($type_value) {
                        case AppWebsiteDecorationModel::TYPE_BOTTOM_MENU:
                            return '底部菜单';

                        case AppWebsiteDecorationModel::TYPE_FIRST_LEVEL:
                            return '一级页面';

                        case AppWebsiteDecorationModel::TYPE_SECONDARY:
                            return '二级页面';
                    }

                    return '';
                }, $app_website_decoration->type);

                return [
                    'admin_user_name' => $app_website_decoration->adminUser->user_name ?? '--',
                    'type_message' => implode('/', $type_message),
                    'version' => $app_website_decoration->version,
                    'type' => $app_website_decoration->type,
                    'name' => $app_website_decoration->name,
                    'id' => $app_website_decoration->id,
                    'alias' => $app_website_decoration->alias,
                    'updated_at' => $app_website_decoration->updated_at->format('Y-m-d H:i:s'),
                    'is_show_bottom' => in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$notShowBottomMapAlias) ? false : true,
                    'is_dir' => in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true) ? true : false, // 判断图标|是否允许装修（是否展示查看页面）
                    'is_allow_decoration' => true, // 判断是否允许装修
                    'children_count' => $app_website_decoration->children_count,
                    'is_show_edit' => in_array($app_website_decoration->alias, [AppWebsiteDecorationModel::ALIAS_HOME, AppWebsiteDecorationModel::ALIAS_SELLER_HOME, AppWebsiteDecorationModel::ALIAS_SELLER_WORKBENCH], true) ? true : false, // 判断是否允许装修
                    'url' => $app_website_decoration->url,
                ];
            });

            return $this->success($data->toArray());
        }

        return view('manage.app_website_decoration.index');
    }

    public function children_index(Request $request)
    {
        $alias = $request->input('alias');
        $number = $request->input('number', 10);

        if ($request->expectsJson()) {
            /**
             * 1:底部菜单 2:一级菜单 3:二级菜单.
             */
            $type = $request->input('type');
            $keywords = $request->input('keywords');
            $query = AppWebsiteDecorationModel::query()->with('adminUser')->withCount('item')->orderByDesc('id');
            $query->whereAlias($alias)->childLevel();
            $query->when($type > 0, function ($type_query) use ($type) {
                $type_query->whereJsonContains('type', (int) $type);
            });
            $query->when($keywords, function ($type_query) use ($keywords) {
                $type_query->where(function ($keywords_query) use ($keywords) {
                    return $keywords_query->where('id', $keywords)->orWhere('name', 'LIKE', "%$keywords%");
                });
            });

            $data = $query->paginate($number);
            $data->getCollection()->transform(function (AppWebsiteDecorationModel $app_website_decoration) {
                $type_message = array_map(function ($type_value) {
                    switch ($type_value) {
                        case AppWebsiteDecorationModel::TYPE_BOTTOM_MENU:
                            return '底部菜单';

                        case AppWebsiteDecorationModel::TYPE_FIRST_LEVEL:
                            return '一级页面';

                        case AppWebsiteDecorationModel::TYPE_SECONDARY:
                            return '二级页面';
                    }

                    return '';
                }, $app_website_decoration->type);

                if ($app_website_decoration->item_count > 0) {
                    $is_show_destroy = Constant::ZERO;
                } else {
                    $is_show_destroy = Constant::ONE;
                }
                $router = null;
                $app_url = $mini_url = $h5_url = '--';

                if ($router) {
                    $h5Param = '';

                    foreach (json_decode($router->params) as $value) {
                        $h5Param .= $value->key.'='.$app_website_decoration->id;
                    }
                    $h5_url = $router->h5_url ? $router->h5_url.connectStr($router->h5_url).$h5Param : '';
                }

                return [
                    'admin_user_name' => $app_website_decoration->adminUser->user_name ?? '--',
                    'type_message' => implode('/', $type_message),
                    'type' => $app_website_decoration->type,
                    'name' => $app_website_decoration->name,
                    'parent_name' => $app_website_decoration->parent->name,
                    'id' => $app_website_decoration->id,
                    'alias' => $app_website_decoration->alias,
                    'updated_at' => $app_website_decoration->updated_at->format('Y-m-d H:i:s'),
                    'is_show' => (string) $app_website_decoration->is_show,
                    'is_show_destroy' => $is_show_destroy,
                    'app_url' => $app_url,
                    'h5_url' => $h5_url,
                    'mini_url' => $mini_url,
                ];
            });

            return $this->success($data->toArray());
        }
        $check_data = [];
        $parent = AppWebsiteDecorationModel::query()->whereAlias($alias)->where('parent_id', Constant::ZERO)->first();
        $parent_name = $parent->name ?? '';
        $parent_id = $parent->id ?? '';

        return view('manage.app_website_decoration.children_index', compact('alias', 'check_data', 'parent_name', 'parent_id'));
    }

    // 复制副本
    public function copy(Request $request)
    {
        $app_website_decoration = AppWebsiteDecorationModel::query()->with('item')->whereId($request->input('id'))->childLevel()->first();

        if (! ($app_website_decoration instanceof AppWebsiteDecorationModel)) {
            return $this->error('未找到网站装修信息');
        }

        if (! in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true)) {
            return $this->error('暂不支持此类型的复制');
        }
        $app_website_decoration_name = $app_website_decoration->name.'-副本';
        $name_count = AppWebsiteDecorationModel::whereName($app_website_decoration_name)->count();

        if ($name_count) {
            $two_count = AppWebsiteDecorationModel::where('name', 'LIKE', "{$app_website_decoration_name}(%")->count();
            $number = $two_count + 1;
            $app_website_decoration_name = "{$app_website_decoration_name}({$number})";
        }
        DBFacade::beginTransaction();

        try {
            $new_app_website_decoration = $app_website_decoration->replicate()->fill(['admin_user_id' => $this->adminUser()->id, 'name' => $app_website_decoration_name]);
            $new_app_website_decoration->save();
            $app_website_decoration->item->map(function (AppWebsiteDecorationItem $item) use ($new_app_website_decoration) {
                $new_item = $item->replicate()->fill(['app_website_decoration_id' => $new_app_website_decoration->id]);
                $new_item->save();
            });
            DBFacade::commit();
        } catch (\Exception) {
            DBFacade::rollBack();

            return $this->error('复制失败');
        }

        return $this->success('复制成功');
    }

    // 删除子页面
    public function destroy(Request $request)
    {
        $app_website_decoration = AppWebsiteDecorationModel::query()->with('item')->withCount('item')->whereId($request->input('id'))->childLevel()->first();

        if (! ($app_website_decoration instanceof AppWebsiteDecorationModel)) {
            return $this->error('未找到网站装修信息');
        }

        if (! in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true)) {
            return $this->error('暂不支持此类型的删除');
        }

        if ($app_website_decoration->item_count > 0) {
            return $this->error('该页面已装修,不支持删除');
        }

        DBFacade::beginTransaction();

        try {
            $message = "删除了移动端装修页面:{$app_website_decoration->name},ID:{$app_website_decoration->id}";
            $app_website_decoration->delete();
            admin_operation_log($request->user(), $message);
            DBFacade::commit();
        } catch (\Exception $exception) {
            DBFacade::rollBack();

            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }

    public function change_is_show(Request $request)
    {
        $app_website_decoration = AppWebsiteDecorationModel::query()->with('item')->whereId($request->input('id'))->childLevel()->first();

        if (! ($app_website_decoration instanceof AppWebsiteDecorationModel)) {
            return $this->error('未找到网站装修信息');
        }

        if (! in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true)) {
            return $this->error('暂不支持此类型的变更状态');
        }
        $change_is_show = ! $app_website_decoration->is_show;

        try {
            $app_website_decoration->is_show = $change_is_show;
            $app_website_decoration->save();
        } catch (\Exception $exception) {
            if ($exception instanceof BusinessException) {
                return $this->error($exception->getMessage());
            }

            return $this->error('校验失败');
        }

        return $this->success([]);
    }

    /**
     * app website decoration detail and check permission.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function edit(Request $request)
    {
        $app_website_decoration = AppWebsiteDecorationModel::query()->whereId($request->input('id'))->first();

        if (! ($app_website_decoration instanceof AppWebsiteDecorationModel)) {
            return $this->error('未找到网站装修信息');
        }
        $app_website_decoration->is_show = (string) $app_website_decoration->is_show;

        $type_map_data = [
            ['label' => '底部菜单', 'value' => AppWebsiteDecorationModel::TYPE_BOTTOM_MENU],
            ['label' => '一级页面', 'value' => AppWebsiteDecorationModel::TYPE_FIRST_LEVEL],
            ['label' => '二级页面', 'value' => AppWebsiteDecorationModel::TYPE_SECONDARY],
        ];
        $info = $app_website_decoration->toArray();

        return $this->success([
            'info' => $info,
            'type_map_data' => $type_map_data,
        ]);
    }

    /**
     * store or update app website decoration.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        $parent_id = $request->input('parent_id');
        $version = $request->input('version');

        try {
            $validate = $request->validate([
                'alias' => 'required',
                'is_show' => 'required|in:'.Constant::ONE.','.Constant::ZERO,
                'image_url' => 'required|url',
                'name' => [
                    'required',
                    'max:8',
                    $id ? \Illuminate\Validation\Rule::unique('app_website_decorations', 'name')->where('version', $version)->where('parent_id', $parent_id)->ignore($id) : \Illuminate\Validation\Rule::unique('app_website_decorations', 'name')->where('version', $version)->where('parent_id', $parent_id),
                ],
                'title' => 'required|max:80',
                'keywords' => 'required|max:100',
                'description' => 'required|max:200',
                'type' => 'required|array',
                'type.*' => 'required|in:'.AppWebsiteDecorationModel::TYPE_BOTTOM_MENU.','.AppWebsiteDecorationModel::TYPE_FIRST_LEVEL.','.AppWebsiteDecorationModel::TYPE_SECONDARY,
            ], [
                'alias.required' => '请设置别名，请刷新页面后重新提交',
                'is_show.required' => '请设置是否显示',
                'is_show.in' => '是否显示格式不正确，请重新设置后再提交',
                'image_url.required' => '请设置小程序入口封面图',
                'image_url.url' => '小程序入口封面图格式不正确',
                'name.required' => '请设置页面名称',
                'name.max' => '页面名称不能超过:max个字符',
                'name.unique' => '该页面名称已存在，请修改！',
                'title.required' => '请填写网页标题',
                'title.max' => '网页标题不能超过:max个字符',
                'keywords.required' => '请填写网页关键词',
                'keywords.max' => '网页关键词不能超过:max个字符',
                'description.required' => '请填写网页描述',
                'description.max' => '网页描述不能超过:max个字符',
                'type.required' => '请设置页面类型',
                'type.array' => '页面类型格式不正确',
                'type.*.required' => '请设置页面类型！',
                'type.*.in' => '页面类型设置不正确，请重新设置',
            ]);
        } catch (\Exception $exception) {
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return $this->error($exception->validator->errors()->first());
            }

            return $this->error('校验失败');
        }

        if (! $id) {
            if (! in_array($validate['alias'], AppWebsiteDecorationModel::$storeMapAlias)) {
                return $this->error('类型错误');
            }
            $parent_website = AppWebsiteDecorationModel::query()->whereAlias($validate['alias'])->whereParentId(Constant::ZERO)->first();

            if (! ($parent_website instanceof AppWebsiteDecorationModel)) {
                return $this->error('未找到网站合集装修信息');
            }
            $data = [
                'alias' => $parent_website->alias,
                'type' => $validate['type'],
                'version' => \App\Models\AppWebsiteDecoration::VERSION_BUYER,
                'is_show' => $validate['is_show'],
                'name' => $validate['name'],
                'parent_id' => $parent_website->id,
                'admin_user_id' => $this->adminUser()->id,
                'image_url' => $validate['image_url'],
                'title' => $validate['title'],
                'keywords' => $validate['keywords'],
                'description' => $validate['description'],
            ];
            $flag = AppWebsiteDecorationModel::query()->create($data);
        } else {
            $app_website_decoration = AppWebsiteDecorationModel::query()->whereId($id)->first();

            if (! $app_website_decoration) {
                return $this->error('未找到网站装修信息');
            }
            $update_data = [
                'type' => $validate['type'],
                'admin_user_id' => $this->adminUser()->id,
                'image_url' => $validate['image_url'],
                'title' => $validate['title'],
                'keywords' => $validate['keywords'],
                'description' => $validate['description'],
            ];

            if ($app_website_decoration->parent_id > 0) {
                $update_data['is_show'] = $validate['is_show'];
                $update_data['name'] = $validate['name'];
            }
            $flag = $app_website_decoration->update($update_data);
        }

        if ($flag) {
            return $this->success([]);
        }

        return $this->error('操作失败，请稍后重试');
    }

    /* 装修开始 */

    /**
     * website decoration component echo data.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|null
     *
     * @throws \App\Exceptions\BusinessException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function decoration(Request $request)
    {
        $id = $request->input('id');
        $app_website_decoration = AppWebsiteDecorationModel::query()->whereId($id)->first();

        if (! $app_website_decoration) {
            return $this->error('未找到网站装修信息');
        }

        if (in_array($app_website_decoration->alias, [AppWebsiteDecorationModel::ALIAS_NOTICE, AppWebsiteDecorationModel::ALIAS_CART, AppWebsiteDecorationModel::ALIAS_DISTRIBUTION], true)) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }

        if (in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true) && ! $app_website_decoration->parent_id) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }
        $app_website_data = $app_website_decoration->toArray();
        // 各个组件针对 home 的依赖(产业链专题装修页面 依赖 首页的 导航栏与标签栏)
        $component_rely_on = [
            AppWebsiteDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL,
            ],
        ];
        $rely_on_data = collect([]);
        $temp_component_rely_on = $component_rely_on[$app_website_decoration->alias] ?? [];

        if (! empty($temp_component_rely_on)) {
            $rely_on_data = AppWebsiteDecorationItem::query()->whereIn('component_name', $component_rely_on[$app_website_decoration->alias] ?? [])->whereHas('app_website_decoration', function ($query) use ($app_website_decoration) {
                if ($app_website_decoration->version == AppWebsiteDecorationModel::VERSION_SELLER) {
                    return $query->where('alias', AppWebsiteDecorationModel::ALIAS_SELLER_HOME);
                }

                return $query->where('alias', AppWebsiteDecorationModel::ALIAS_HOME);
            })->get()->map(function (AppWebsiteDecorationItem $item) {
                return ComponentFactory::getComponent($item->component_name)->display($item->toArray());
            });

            if ($rely_on_data->isEmpty()) {
                return $this->error('当前页面的部分组件(导航栏或标签栏)依赖首页设置，请先前往首页设置后再进行装修');
            }
        }
        $item_data = $app_website_decoration->item;
        /* 不参与循环的组件 */
        $not_for_names = [
            AppWebsiteDecorationModel::ALIAS_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_LABEL,
                AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV,
                AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN,
                AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
                AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL,
            ],
        ];

        try {
            [$component_icon, $component_value, $not_items_fixed_value] = app(PageDefaultDict::class)->commonMap($app_website_decoration->alias);
            if ($item_data->isEmpty()) {
                $temp_data = $not_items_fixed_value;
            } else {
                $temp_data = $item_data->map(function (AppWebsiteDecorationItem $item) {
                    return ComponentFactory::getComponent($item->component_name)->display($item->toArray());
                })->toArray();
            }

            $temp_data = collect($temp_data)->merge($rely_on_data)->values();
            $data = $temp_data->whereNotIn('component_name', $not_for_names[$app_website_decoration->alias] ?? [])->values()->toArray();
            $not_for_data = $temp_data->whereIn('component_name', $not_for_names[$app_website_decoration->alias] ?? [])->values()->toArray();
        } catch (\Exception $exception) {
            if ($exception instanceof BusinessException) {
                return $this->error($exception->getMessage());
            }

            return $this->error('初始化失败');
        }

        if ($request->expectsJson()) {
            return $this->success(compact('component_icon', 'component_value', 'data', 'app_website_data', 'not_for_data'));
        }

        if ($app_website_decoration->alias === AppWebsiteDecorationModel::ALIAS_CATEGORY_LIST) {
            $temp_alias = AppWebsiteDecorationModel::ALIAS_CATEGORY;

            return view("admin.app_website_decoration.{$temp_alias}_form", compact('component_icon', 'component_value', 'data', 'app_website_data', 'not_for_data'));
        }

        return view("manage.app_website_decoration.{$app_website_decoration->alias}_form", compact('component_icon', 'component_value', 'data', 'app_website_data', 'not_for_data'));
    }

    /**
     * store or update decoration items.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function decoration_store(Request $request)
    {
        $id = $request->input('id');
        $data = $request->input('data');
        $app_website_decoration = AppWebsiteDecorationModel::query()->whereId($id)->first();

        if (! $app_website_decoration) {
            return $this->error('未找到网站装修信息');
        }
        // 组件针对页面唯一
        $page_only_names = [
            AppWebsiteDecorationModel::ALIAS_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV,
                AppWebsiteDecorationItem::COMPONENT_NAME_LABEL,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_GOODS_DATA,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_ORDER_DATA,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_BUSINESS_DATA,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_HELP_CENTER,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV,
            ],
        ];
        $only_names = $page_only_names[$app_website_decoration->alias] ?? [];

        if (! empty($only_names)) {
            foreach ($only_names as $only_name) {
                $temp_only_item = collect($data)->where('component_name', $only_name)->values();

                if ($temp_only_item->count() > 1) {
                    return $this->error(($temp_only_item->first()['name'] ?? '').'组件具备唯一特性，无法设置多次，请调整后进行修改');
                }
            }
        }
        // 固定组件中 某些页面是必须传的 如果不传异常提示
        $fixed_component_must = [
            AppWebsiteDecorationModel::ALIAS_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_LABEL,
                AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV,
                AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN,
                AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
                AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_HOME => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL,
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO,
            ],
            AppWebsiteDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV,
            ],
        ];
        /* 组件中文名称 */
        $component_chinese_name = [
            AppWebsiteDecorationItem::COMPONENT_NAME_LABEL => '标签栏',
            AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV => '导航栏',
            AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN => '大屏广告位',
            AppWebsiteDecorationItem::COMPONENT_NAME_RED_ENVELOPE => '签到送红包',
            AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING => '侧边广告位',
            AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT => '二楼广告位',
            AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL => '标签栏',
            AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO => '店铺信息',
            AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV => '店铺导航',
            AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_LABEL => '标签栏',
            AppWebsiteDecorationItem::COMPONENT_NAME_TRY_LABEL => '标签栏',
        ];
        $temp_fixed_component_must = $fixed_component_must[$app_website_decoration->alias] ?? [];

        if (! empty($temp_fixed_component_must)) {
            foreach ($temp_fixed_component_must as $temp_fixed_component_item) {
                $is_exists_request = collect($data)->where('component_name', $temp_fixed_component_item)->values()->count();

                if (! $is_exists_request) {
                    return $this->error(($component_chinese_name[$temp_fixed_component_item] ?? '').'组件必须设置，请调整后进行修改');
                }
            }
        }

        /* 全局唯一组件校验 只限 导航栏与标签栏 */
        if ($app_website_decoration->alias !== AppWebsiteDecorationModel::ALIAS_HOME) {
            $temp_globally_unique = collect($data)->whereIn('component_name', [AppWebsiteDecorationItem::COMPONENT_NAME_LABEL, AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV])->values();

            if ($temp_globally_unique->count() > 0) {
                return $this->error(($temp_globally_unique->first()['name'] ?? '').'组件具备全局唯一特性，无法设置多次，请前往首页进行设置');
            }
        }

        if (in_array($app_website_decoration->alias, [AppWebsiteDecorationModel::ALIAS_NOTICE, AppWebsiteDecorationModel::ALIAS_CART, AppWebsiteDecorationModel::ALIAS_DISTRIBUTION], true)) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }

        if (in_array($app_website_decoration->alias, AppWebsiteDecorationModel::$storeMapAlias, true) && ! $app_website_decoration->parent_id) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }

        /* my order component is special check */
        if (empty($data) && $app_website_decoration->alias !== AppWebsiteDecorationModel::ALIAS_ORDER) {
            return $this->error('非我的订单不支持所有组件设置为空');
        }

        $update_data = [];
        $insert_data = [];

        try {
            foreach ($data as $key => $datum) {
                if (! isset($datum['component_name'])) {
                    continue;
                }
                $temp_item = ComponentFactory::getComponent($datum['component_name'], $datum['name'])->validate($datum);
                $temp_item['sort'] = $key + 1;

                if (isset($temp_item['id']) && $temp_item['id'] > 0) {
                    $update_data[] = $temp_item;
                } else {
                    $insert_data[] = $temp_item;
                }
            }
        } catch (\Exception $exception) {
            if ($exception instanceof BusinessException) {
                return $this->error($exception->getMessage());
            }

            return $this->error('校验失败');
        }

        /* 拆分数据 */
        $temp_update_item_ids = array_column($update_data, 'id');
        $delete_item_ids = $app_website_decoration->item->filter(function (AppWebsiteDecorationItem $item) use ($temp_update_item_ids) {
            return ! in_array($item->id, $temp_update_item_ids);
        })->pluck('id')->toArray();
        DBFacade::beginTransaction();

        try {
            /* $data is empty clear decoration items data */
            if (empty($data)) {
                if ($app_website_decoration->alias === AppWebsiteDecorationModel::ALIAS_ORDER) {
                    $app_website_decoration->item()->delete();
                }
            } else {
                /* 先删除掉需要删除的 */
                if (! empty($delete_item_ids)) {
                    $app_website_decoration->item()->whereIn('id', $delete_item_ids)->delete();
                }

                /* 处理新增的 */
                if (! empty($insert_data)) {
                    foreach ($insert_data as $insert_datum) {
                        $app_website_decoration->item()->create($insert_datum);
                    }
                }

                /* 处理更新的 */
                if (! empty($update_data)) {
                    foreach ($update_data as $update_datum) {
                        $app_website_decoration->item()->where('id', $update_datum['id'])->update($update_datum);
                    }
                }
            }
            /* app website decoration table update */
            $app_website_decoration->admin_user_id = $this->adminUser()->id;
            $app_website_decoration->save();
            DBFacade::commit();
        } catch (\Exception $exception) {
            DBFacade::rollBack();

            return $this->error('装修失败，请稍后重试'.$exception->getMessage());
        }
        $this->clearCache($app_website_decoration);

        return $this->success([]);
    }

    /**
     * 根据类型获取画布展示数据.
     *
     * @return \Illuminate\Http\JsonResponse|null
     *
     * @throws \App\Exceptions\BusinessException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_content_data_by_alias(Request $request)
    {
        $component_name = $request->input('component_name'); // 页面别名

        if (! $component_name || is_null($component_name)) {
            return $this->error('请先选择组件');
        }
        $name = $request->input('name', '');
        $component_data = $request->input('component_data'); // 板块data

        try {
            $component = ComponentFactory::getComponent($component_name, $name ?? '');
            $data = $component->getContent($component->validate($component_data));

            return $this->success($data);
        } catch (\Exception $exception) {
            if ($exception instanceof BusinessException) {
                return $this->error($exception->getMessage());
            }

            return $this->error('请求失败，请稍后重试');
        }
    }

    /**
     * 根据分类id 和栏目id获取商品数据.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_hot_sale_goods_by_cat_id(Request $request)
    {
        $content = $request->input('content'); // item_id
        $cat_id = $request->input('cat_id'); // cat_id

        if (! $content || ! $cat_id) {
            return $this->error('栏目内容 和 分类id 不能为空');
        }

        if (! is_array($content)) {
            return $this->error('栏目内容格式不正确');
        }
        $goods = app(AppWebsiteDecorationItemDao::class)->hotGoodByItemCatId(0, $content, $cat_id);

        return $this->success($goods);
    }

    /**
     * 新闻分类+文章
     * 根据分类id 和content获取二级分类+文章.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_second_cats_news(Request $request)
    {
        $content = $request->input('content'); // item_id
        $cat_id = $request->input('cat_id'); // cat_id

        if (! $content || ! $cat_id) {
            return $this->error('栏目内容 和 分类id 不能为空');
        }

        if (! is_array($content)) {
            return $this->error('栏目内容格式不正确');
        }
        $info = app(AppWebsiteDecorationItemDao::class)->secondCatsNews(0, $content, $cat_id);

        return $this->success($info);
    }

    public function router_options()
    {
        return $this->success((new MobileRouterService)->routers());
    }

    /**
     * 远端搜索.
     *
     * @page_type 页面类型
     *
     * @keywords 关键字
     *
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_options(Request $request)
    {
        $page_type = $request->input('page_type');
        $keywords = $request->input('keywords');
        $data = (new RomoteSearchService)->getOption($page_type, $keywords);

        return $this->success($data);
    }

    /**
     * 远端搜索.
     *
     * @page_type 页面类型
     *
     * @keywords 关键字
     *
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function router_search(Request $request)
    {
        $alias = $request->input('alias');
        $keywords = $request->input('keywords');
        $data = (new MobileRouterService)->getOption($alias, $keywords);

        return $this->success($data);
    }

    /**
     * 远端搜索.
     *
     * @page_type 分类搜索
     *
     * @keywords 关键字
     *
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_cat_list(Request $request)
    {
        $keywords = $request->input('keywords');
        $data = (new RomoteSearchService)->getCatList($keywords);

        return $this->success($data);
    }

    /**
     * 卖家版-获取跳转链接.
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function seller_router_options()
    {
        return $this->success((new SellerMobileRouterService)->routers());
    }

    /**
     * 卖家版远端搜索.
     *
     * @page_type 页面类型
     *
     * @keywords 关键字
     *
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function seller_router_search(Request $request)
    {
        $alias = $request->input('alias');
        $keywords = $request->input('keywords');
        $data = (new SellerMobileRouterService)->getOption($alias, $keywords);

        return $this->success($data);
    }

    /**
     * ajax获取试样商品
     * 根据分类名称 和栏目id获取试样商品数据.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_free_try_goods(Request $request)
    {
        $content = $request->input('content'); // item_id
        $cat_name = $request->input('cat_name'); // cat_name

        if (! $content || ! $cat_name) {
            return $this->error('栏目内容 和 分类名称 不能为空');
        }

        if (! is_array($content)) {
            return $this->error('栏目内容格式不正确');
        }
        $goods = app(AppWebsiteDecorationItemDao::class)->freeTryByItemCatId(0, $content, $cat_name);

        return $this->success($goods);
    }

    /**
     * ajax获取试用商品
     * 根据分类名称 和栏目id获取试用商品数据.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get_charge_try_goods(Request $request)
    {
        $content = $request->input('content'); // item_id
        $cat_name = $request->input('cat_name'); // cat_name

        if (! $content || ! $cat_name) {
            return $this->error('栏目内容 和 分类名称 不能为空');
        }

        if (! is_array($content)) {
            return $this->error('栏目内容格式不正确');
        }
        $goods = app(AppWebsiteDecorationItemDao::class)->chargeTryByItemCatId(0, $content, $cat_name);

        return $this->success($goods);
    }

    /**
     * 清除缓存.
     *
     * @return void
     */
    private function clearCache(AppWebsiteDecorationModel $app_website_decoration)
    {
        switch ($app_website_decoration->alias) {
            case AppWebsiteDecorationModel::ALIAS_HOME:
                /* 清除首页缓存 */
                foreach (AppWebsiteDecorationModel::$homeCacheMapAlias as $home_cache_alias) {
                    Cache::forget($home_cache_alias);
                }

                break;

            case AppWebsiteDecorationModel::ALIAS_CATEGORY:
            case AppWebsiteDecorationModel::ALIAS_CATEGORY_LIST:
                Cache::forget('mobile_'.\App\Models\AppWebsiteDecoration::ALIAS_CATEGORY.'_'.$app_website_decoration->id);

                break;

            case AppWebsiteDecorationModel::ALIAS_INDUSTRIAL:
                /* 清除产业链页缓存 */
                foreach (AppWebsiteDecorationModel::$industrialCacheMapAlias as $industrial_cache_alias) {
                    Cache::forget($industrial_cache_alias);
                }

                break;

            case AppWebsiteDecorationModel::ALIAS_PUBLIC:
                /* 清除公共页缓存 */
                foreach (AppWebsiteDecorationModel::$publicCacheMapAlias as $public_cache_alias) {
                    Cache::forget($public_cache_alias);
                }

                break;

            case AppWebsiteDecorationModel::ALIAS_GOODS:
                /* 清除商品详情配置缓存 */
                Cache::forget(AppWebsiteDecorationModel::MOBILE_GOODS_CONFIG);

                break;
        }
    }
}
