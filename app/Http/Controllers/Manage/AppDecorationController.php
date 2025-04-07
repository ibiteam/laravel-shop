<?php

namespace App\Http\Controllers\Manage;

use App\Components\ComponentFactory;
use App\Components\PageDefaultDict;
use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AppDecoration as AppDecorationModel;
use App\Models\AppDecorationItem;
use App\Utils\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as DBFacade;

class AppDecorationController extends BaseController
{
    public function index(Request $request)
    {
        $query = AppDecorationModel::query()
            ->with('adminUser:id,user_name')
            ->withCount('children')
            ->orderBy('id')
            ->whereParentId(Constant::ZERO);
        $list = $query->paginate($request->input('number', 10));
        $list->getCollection()->transform(function (AppDecorationModel $app_decoration) {
            $app_decoration->admin_user_name = $app_decoration->adminUser?->user_name ?? '--';

            return $app_decoration;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    public function decoration(Request $request)
    {
        $id = $request->input('id');
        $app_website_decoration = AppDecorationModel::query()->whereId($id)->first();

        if (! $app_website_decoration) {
            return $this->error('未找到网站装修信息');
        }
        $app_website_data = $app_website_decoration->toArray();
        $rely_on_data = collect();
        $temp_component_rely_on = $component_rely_on[$app_website_decoration->alias] ?? [];

        if (! empty($temp_component_rely_on)) {
            $rely_on_data = AppDecorationItem::query()
                ->whereIn('component_name', $component_rely_on[$app_website_decoration->alias] ?? [])
                ->whereHas('app_website_decoration', function ($query) use ($app_website_decoration) {
                    return $query->where('alias', AppDecorationModel::ALIAS_HOME);
                })->get()->map(function (AppDecorationItem $item) {
                    return ComponentFactory::getComponent($item->component_name)->display($item->toArray());
                });

            if ($rely_on_data->isEmpty()) {
                return $this->error('当前页面的部分组件(导航栏或标签栏)依赖首页设置，请先前往首页设置后再进行装修');
            }
        }
        $item_data = $app_website_decoration->item;
        /* 不参与循环的组件 */
        $not_for_names = [
            AppDecorationModel::ALIAS_HOME => [
//                AppDecorationItem::COMPONENT_NAME_LABEL,
//                AppDecorationItem::COMPONENT_NAME_HOME_NAV,
//                AppDecorationItem::COMPONENT_NAME_LARGE_SCREEN,
//                AppDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
//                AppDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT,
            ],
        ];

        try {
            [$component_icon, $component_value, $not_items_fixed_value] = app(PageDefaultDict::class)->commonMap($app_website_decoration->alias);
            if ($item_data->isEmpty()) {
                $temp_data = $not_items_fixed_value;
            } else {
                $temp_data = $item_data->map(function (AppDecorationItem $item) {
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

        return $this->success([
            'component_icon' => $component_icon,
            'component_value' => $component_value,
            'data' => $data,
            'app_website_data' => $app_website_data,
            'not_for_data' => $not_for_data,
        ]);
    }

    public function decorationStore(Request $request)
    {
        $id = $request->input('id');
        $data = $request->input('data');
        $app_website_decoration = AppDecorationModel::query()->whereId($id)->first();

        if (! $app_website_decoration) {
            return $this->error('未找到网站装修信息');
        }
        // 组件针对页面唯一
        $page_only_names = [
            AppDecorationModel::ALIAS_HOME => [
                AppDecorationItem::COMPONENT_NAME_HOME_NAV,
                AppDecorationItem::COMPONENT_NAME_LABEL,
            ],
            AppDecorationModel::ALIAS_SELLER_HOME => [
                AppDecorationItem::COMPONENT_NAME_SELLER_LABEL,
                AppDecorationItem::COMPONENT_NAME_SELLER_GOODS_DATA,
                AppDecorationItem::COMPONENT_NAME_SELLER_ORDER_DATA,
                AppDecorationItem::COMPONENT_NAME_SELLER_BUSINESS_DATA,
                AppDecorationItem::COMPONENT_NAME_SELLER_HELP_CENTER,
            ],
            AppDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV,
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
            AppDecorationModel::ALIAS_HOME => [
                AppDecorationItem::COMPONENT_NAME_LABEL,
                AppDecorationItem::COMPONENT_NAME_HOME_NAV,
                AppDecorationItem::COMPONENT_NAME_LARGE_SCREEN,
                AppDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
                AppDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT,
            ],
            AppDecorationModel::ALIAS_SELLER_HOME => [
                AppDecorationItem::COMPONENT_NAME_SELLER_LABEL,
                AppDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO,
            ],
            AppDecorationModel::ALIAS_SELLER_WORKBENCH => [
                AppDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV,
            ],
        ];
        /* 组件中文名称 */
        $component_chinese_name = [
            AppDecorationItem::COMPONENT_NAME_LABEL => '标签栏',
            AppDecorationItem::COMPONENT_NAME_HOME_NAV => '导航栏',
            AppDecorationItem::COMPONENT_NAME_LARGE_SCREEN => '大屏广告位',
            AppDecorationItem::COMPONENT_NAME_RED_ENVELOPE => '签到送红包',
            AppDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING => '侧边广告位',
            AppDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT => '二楼广告位',
            AppDecorationItem::COMPONENT_NAME_SELLER_LABEL => '标签栏',
            AppDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO => '店铺信息',
            AppDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV => '店铺导航',
            AppDecorationItem::COMPONENT_NAME_ZIXUN_LABEL => '标签栏',
            AppDecorationItem::COMPONENT_NAME_TRY_LABEL => '标签栏',
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
        if ($app_website_decoration->alias !== AppDecorationModel::ALIAS_HOME) {
            $temp_globally_unique = collect($data)->whereIn('component_name', [AppDecorationItem::COMPONENT_NAME_LABEL, AppDecorationItem::COMPONENT_NAME_HOME_NAV])->values();

            if ($temp_globally_unique->count() > 0) {
                return $this->error(($temp_globally_unique->first()['name'] ?? '').'组件具备全局唯一特性，无法设置多次，请前往首页进行设置');
            }
        }

        if (in_array($app_website_decoration->alias, [AppDecorationModel::ALIAS_NOTICE, AppDecorationModel::ALIAS_CART, AppDecorationModel::ALIAS_DISTRIBUTION], true)) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }

        if (in_array($app_website_decoration->alias, AppDecorationModel::$storeMapAlias, true) && ! $app_website_decoration->parent_id) {
            return $this->error("暂不支持对 {$app_website_decoration->name} 进行装修");
        }

        /* my order component is special check */
        if (empty($data) && $app_website_decoration->alias !== AppDecorationModel::ALIAS_ORDER) {
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
        $delete_item_ids = $app_website_decoration->item->filter(function (AppDecorationItem $item) use ($temp_update_item_ids) {
            return ! in_array($item->id, $temp_update_item_ids);
        })->pluck('id')->toArray();
        DBFacade::beginTransaction();

        try {
            /* $data is empty clear decoration items data */
            if (empty($data)) {
                if ($app_website_decoration->alias === AppDecorationModel::ALIAS_ORDER) {
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
}
