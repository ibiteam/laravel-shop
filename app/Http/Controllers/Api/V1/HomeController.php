<?php

namespace App\Http\Controllers\Api\V1;

use App\Components\ComponentFactory;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\AppDecoration;
use App\Models\AppDecorationItem;
use App\Services\AppDecoration\AppDecorationService;
use App\Services\Goods\GoodsService;
use App\Utils\Constant;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    // 首页
    public function home(AppDecorationService $app_decoration_service)
    {
        $decoration = AppDecoration::query()->whereAlias(AppDecoration::ALIAS_HOME)->with('item')->first();
        if (!$decoration) {
            return $this->error('未找到页面');
        }
        $items = $decoration->item()->where('is_show', Constant::ONE)->orderBy('sort')->get();

        if ($items->isEmpty()) {
            return $this->error('页面尚未装修');
        }
        try {
            // 首页主体数据
            $data = $app_decoration_service->homeData($items);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage());
        }

        return $this->success(array_merge($data, [
            'title' => $decoration->title ?: $decoration->name,
            'keywords' => $decoration->keywords,
            'description' => $decoration->description
        ]));
    }

    // 预览
    public function preview(Request $request, AppDecorationService $app_decoration_service)
    {
        $id = $request->get('id');
        $decoration = AppDecoration::query()->whereId($id)->with('item')->first();
        if (!$decoration) {
            return $this->error('未找到页面');
        }
        // 查询历史记录中最新一条 对应的草稿数据
        $item_data = $app_decoration_service->getLatestDraftItems($decoration);

        if ($item_data->isEmpty()) {
            return $this->error('页面尚未装修');
        }

        try {
            switch ($decoration->alias) {
                case AppDecoration::ALIAS_HOME:
                    // 主体数据
                    $data = $app_decoration_service->homeData($item_data, true);
                    break;
                default:
                    return $this->error('页面尚未装修');
            }
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage());
        }

        return $this->success(array_merge($data, [
            'title' => $decoration->title ?: $decoration->name,
            'keywords' => $decoration->keywords,
            'description' => $decoration->description
        ]));
    }

    // 搜索
    public function search()
    {
        $app_decoration = AppDecoration::query()->whereAlias(AppDecoration::ALIAS_HOME)->with([
            'item' => function ($item_query) {
                return $item_query->where('is_show', Constant::ONE);
            },
        ])->first();
        if (!($app_decoration instanceof AppDecoration)) {
            return $this->error('首页尚未装修!');
        }
        $app_decoration_item = $app_decoration->item->filter(function (AppDecorationItem $item) {
            return AppDecorationItem::COMPONENT_NAME_HOME_NAV === $item->component_name;
        })->first();
        if (!($app_decoration_item instanceof AppDecorationItem)) {
            return $this->error('导航搜索尚未装修');
        }

        try {
            $temp_home_nav = $app_decoration_item->toArray();
            /* fixed components：home nav */
            $home_nav_content = ComponentFactory::getComponent($app_decoration_item->component_name, $app_decoration_item->name)->getContent($temp_home_nav);

            return $this->success($home_nav_content);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage());
        } catch (\Exception) {
            return $this->error('获取搜索组件失败');
        }
    }

    // 为您推荐
    public function recommend(Request $request, GoodsService $goods_service)
    {
        return $this->success($goods_service->getRecommendData($request->get('no')));
    }
}
