<?php

namespace App\Components;

use App\Components\AppComponents\AdvertComponent;
use App\Components\AppComponents\AdvertisingBannerComponent;
use App\Components\AppComponents\GoodsRecommendComponent;
use App\Components\AppComponents\HomeNavComponent;
use App\Components\AppComponents\HorizontalCarouselComponent;
use App\Components\AppComponents\HotZoneComponent;
use App\Components\AppComponents\LabelComponent;
use App\Components\AppComponents\QuickLinkComponent;
use App\Exceptions\BusinessException;
use App\Models\AppDecorationItem;

class ComponentFactory
{
    /**
     * get PageComponent by name.
     *
     * @param string $name component name | param option
     *
     * @throws BusinessException
     */
    public static function getComponent(string $alias, string $name = ''): PageComponent
    {
        $components = [
            AppDecorationItem::COMPONENT_NAME_HOME_NAV => HomeNavComponent::class, // 导航搜索
            AppDecorationItem::COMPONENT_NAME_LABEL => LabelComponent::class, // 导航搜索
            AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT => AdvertComponent::class, // 弹屏广告
            AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT => AdvertComponent::class, // 悬浮广告
            AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL => HorizontalCarouselComponent::class, // 轮播图
            AppDecorationItem::COMPONENT_NAME_QUICK_LINK => QuickLinkComponent::class, // 金刚区
            AppDecorationItem::COMPONENT_NAME_ADVERTISING_BANNER => AdvertisingBannerComponent::class, // 广告位
            AppDecorationItem::COMPONENT_NAME_HOT_ZONE => HotZoneComponent::class, // 热区
            AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND => GoodsRecommendComponent::class, // 商品推荐
        ];

        if (! isset($components[$alias])) {
            throw new BusinessException('没有找到'.$alias.'组件！');
        }
        $class = $components[$alias];
        $page_component = new $class($alias, $name);

        // 实例化组件
        if (! $page_component instanceof PageComponent) {
            throw new BusinessException($class.' is not instance of PageComponent');
        }

        return $page_component;
    }
}
