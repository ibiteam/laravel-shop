<?php

namespace App\Http\Controllers\Api\V1;

use App\Components\ComponentFactory;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\AppDecoration;
use App\Models\AppDecorationItem;
use App\Services\Goods\GoodsService;
use App\Utils\Constant;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    // 首页
    public function home()
    {

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
            return $this->error('页面尚未装修!');
        }
        $app_decoration_item = $app_decoration->item->filter(function (AppDecorationItem $item) {
            return AppDecorationItem::COMPONENT_NAME_HOME_NAV === $item->component_name;
        })->first();
        if (!($app_decoration_item instanceof AppDecorationItem)) {
            return $this->error('页面尚未装修');
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
    public function recommend(GoodsService $goods_service)
    {
        return $this->success($goods_service->getRecommendData());
    }
}
