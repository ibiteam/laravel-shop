<?php

namespace App\Services\AppDecoration;

use App\Components\ComponentFactory;
use App\Exceptions\BusinessException;
use App\Models\AppDecoration;
use App\Models\AppDecorationItem;
use App\Models\AppDecorationItemDraft;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class AppDecorationService
{
    // 缓存数据
    private static $cache_component_name = [
        AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
        AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
        AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL,
        AppDecorationItem::COMPONENT_NAME_QUICK_LINK,
        AppDecorationItem::COMPONENT_NAME_ADVERTISING_BANNER,
        AppDecorationItem::COMPONENT_NAME_HOT_ZONE,
        AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND,
        AppDecorationItem::COMPONENT_NAME_RECOMMEND,
    ];

    private static $special_cache_name = [
        AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
        AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
    ];

    // 不缓存数据
    private static $not_cache_component_name = [
        AppDecorationItem::COMPONENT_NAME_HOME_NAV,
    ];


    // 获取当前装修项的最新草稿数据
    public function getLatestDraftItems(AppDecoration $app_decoration)
    {
        $latest_log = app(AppDecorationLogService::class)->getLatestLog($app_decoration->id);

        if (!$latest_log) {
            return collect();
        }

        return $app_decoration->itemDraft()->whereIn('id', $latest_log->app_decoration_item_ids)->get();
    }

    // 发布装修
    public function issueDecoration(AppDecoration $app_decoration)
    {
        // 获取当前装修项的最新草稿数据
        $draftItems = $this->getLatestDraftItems($app_decoration);
        // 清空正式表中对应的装修项数据
        AppDecorationItem::whereAppDecorationId($app_decoration->id)->delete();
        // 准备同步数据
        $syncData = $draftItems->map(function (AppDecorationItemDraft $draftItem) use ($app_decoration) {
            return [
                'app_decoration_id' => $app_decoration->id,
                'name' => $draftItem->name,
                'component_name' => $draftItem->component_name,
                'is_show' => $draftItem->is_show,
                'sort' => $draftItem->sort,
                'content' => json_encode($draftItem->content, JSON_UNESCAPED_UNICODE),
                'is_fixed_assembly' => $draftItem->is_fixed_assembly,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
        // 批量插入到 AppDecorationItem 表
        AppDecorationItem::insert($syncData);
    }

    // 校验组件
    public function validateComponentData(AppDecoration $app_decoration, $data)
    {
        // 组件针对页面唯一
        $page_only_names = [
            AppDecoration::ALIAS_HOME => [
                AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
                AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
            ],
        ];
        $only_names = $page_only_names[$app_decoration->alias] ?? [];

        if (! empty($only_names)) {
            foreach ($only_names as $only_name) {
                $temp_only_item = collect($data)->where('component_name', $only_name)->values();

                if ($temp_only_item->count() > 1) {
                    throw new BusinessException(($temp_only_item->first()['name'] ?? '').'组件具备唯一特性，无法设置多次，请调整后进行修改');
                }
            }
        }
        // 固定组件中 某些页面是必须传的 如果不传异常提示
        $fixed_component_must = [
            AppDecoration::ALIAS_HOME => [
                AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
                AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
            ],
        ];
        /* 组件中文名称 */
        $component_chinese_name = [
            AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT => '弹屏广告',
            AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT => '悬浮广告',
        ];
        $temp_fixed_component_must = $fixed_component_must[$app_decoration->alias] ?? [];

        if (! empty($temp_fixed_component_must)) {
            foreach ($temp_fixed_component_must as $temp_fixed_component_item) {
                $is_exists_request = collect($data)->where('component_name', $temp_fixed_component_item)->values()->count();

                if (! $is_exists_request) {
                    throw new BusinessException(($component_chinese_name[$temp_fixed_component_item] ?? '').'组件必须设置，请调整后进行修改');
                }
            }
        }

        /* 全局唯一组件校验 只限 导航栏与标签栏 */
        if ($app_decoration->alias !== AppDecoration::ALIAS_HOME) {
            $temp_globally_unique = collect($data)->whereIn('component_name', [AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT, AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT])->values();

            if ($temp_globally_unique->count() > 0) {
                throw new BusinessException(($temp_globally_unique->first()['name'] ?? '').'组件具备全局唯一特性，无法设置多次，请前往首页进行设置');
            }
        }
    }

    /**
     * 获取装修信息
     *
     * @param int $id
     * @return AppDecoration
     * @throws ModelNotFoundException
     */
    public function getAppDecoration(int $id): AppDecoration
    {
        return AppDecoration::query()->findOrFail($id);
    }

    /**
     * 处理日志记录
     *
     * @param AppDecorationLogService $app_decoration_log_service
     * @param AppDecoration $app_decoration
     * @param array $item_ids
     * @param int $button_type
     * @return void
     */
    public function handleLog(AppDecorationLogService $app_decoration_log_service, AppDecoration $app_decoration, array $item_ids, int $button_type, int $admin_user_id): void
    {
        if ($button_type === AppDecoration::OPERATE_TYPE_RELEASE) {
            // 发布，更新最新一次草稿，然后取最新草稿的数据进行发布 更新 - 记录、草稿数据
            $app_decoration_log = $app_decoration_log_service->getLatestLog($app_decoration->id);
            $app_decoration_log_service->saveLog($app_decoration_log, $app_decoration->id, $item_ids, $admin_user_id);
            // 发布装修
            $this->issueDecoration($app_decoration);
            $app_decoration->release_time = now();
            $app_decoration->save();
        } else {
            // 保存草稿|预览 提交 记录 草稿数据
            $app_decoration_log_service->saveLog(null, $app_decoration->id, $item_ids, $admin_user_id);
        }
    }

    // 首页数据
    public function homeData(Collection $items)
    {
//        $home_nav_item = $items->where('component_name', AppDecorationItem::COMPONENT_NAME_HOME_NAV)->first();
//        if (!($home_nav_item instanceof AppDecorationItem)) {
//            return $this->error('页面搜索尚未装修');
//        }
//        // 搜索组件
//        $home_nav = ComponentFactory::getComponent($home_nav_item->component_name, $home_nav_item->name)->getContent($home_nav_item->toArray());
        $home_nav = [];
        $now = now();
        $cache_name = AppDecoration::MOBILE_HOME_BY_H5;
        // 正式环境 - 缓存到当天 23:59:59
        if (is_pro_env()) {
            $cache_data = Cache::remember($cache_name, $now->diffInSeconds($now->copy()->endOfDay()), function () use ($items) {
                return $items->whereIn('component_name', self::$cache_component_name)->map(function (AppDecorationItem $item) {
                    $temp_item = $item->toArray();

                    return ComponentFactory::getComponent($item->component_name, $item->name)->getContent($temp_item);
                });
            });
        } else {
            $cache_data = $items->whereIn('component_name', self::$cache_component_name)->map(function (AppDecorationItem $item) {
                $temp_item = $item->toArray();

                return ComponentFactory::getComponent($item->component_name, $item->name)->getContent($temp_item);
            });
        }

        $not_cache_data = $items->whereIn('component_name', self::$not_cache_component_name)->map(function (AppDecorationItem $item) {
            $info = $item->toArray();

            return ComponentFactory::getComponent($item->component_name, $item->name)->getContent($info);
        });
        // 特殊的组件数据
        $special_cache_data = $cache_data->whereIn('component_name', self::$special_cache_name);
        // 不包含特殊组件的数据
        $new_cache_data = $cache_data->whereNotIn('component_name', self::$special_cache_name);
        // 主体数据
        $content = $new_cache_data->merge($not_cache_data)->filter(function ($item) {
            return !empty($item);
        })->sortBy('sort')->values();
        // 弹屏广告组件
        $danping_advertisement = $special_cache_data->where('component_name', AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT)->first();
        // 悬浮广告组件
        $suspended_advertisement = $special_cache_data->where('component_name', AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT)->first();

        return [
            'danping_advertisement' => $danping_advertisement,
            'suspended_advertisement' => $suspended_advertisement,
            'home_nav' => $home_nav,
            'content' => $content,
        ];
    }
}
