<?php

namespace App\Components;

use App\Components\AppComponents\AdvertComponent;
use App\Components\AppComponents\AdvertisingComponent;
use App\Components\AppComponents\AdvertisingThreeComponent;
use App\Components\AppComponents\BuyAndSellComponent;
use App\Components\AppComponents\ChannelSquareComponent;
use App\Components\AppComponents\HomeNavColumnComponent;
use App\Components\AppComponents\LabelColumnComponent;
use App\Components\AppComponents\MineCustomComponent;
use App\Components\AppComponents\MyAssetComponent;
use App\Components\AppComponents\OrderCenterComponent;
use App\Components\AppComponents\QuickLinkComponent;
use App\Components\AppComponents\RecommendComponent;
use App\Components\AppComponents\RecommendSellerComponent;
use App\Components\AppComponents\ThemeAdvertisingComponent;
use App\Components\SellerCenterComponents\AdvertisingComponent as SellerAdvertisingComponent;
use App\Components\SellerCenterComponents\BusinessDataComponent;
use App\Components\SellerCenterComponents\GoodsDataComponent;
use App\Components\SellerCenterComponents\HelpCenterComponent;
use App\Components\SellerCenterComponents\LabelComponent as SellerLabelComponent;
use App\Components\SellerCenterComponents\OrderDataComponent;
use App\Components\SellerCenterComponents\ShopInfoComponent;
use App\Components\SellerCenterComponents\StoreNavComponent;
use App\Components\SellerEnter\CheckboxComponent;
use App\Components\SellerEnter\DateComponent;
use App\Components\SellerEnter\FileComponent;
use App\Components\SellerEnter\InputComponent;
use App\Components\SellerEnter\MoreFileComponent;
use App\Components\SellerEnter\RadioComponent;
use App\Components\SellerEnter\SelectComponent;
use App\Components\SellerEnter\TextareaComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\SellerEnterConfig;


class ComponentFactory
{

    /**
     * @throws BusinessException
     */
    public static function getSellerEnterComponent(string $component_name, string $name = '')
    {
        $components = [
            SellerEnterConfig::TYPE_TEXT => InputComponent::class,
            SellerEnterConfig::TYPE_FILE => FileComponent::class,
            SellerEnterConfig::TYPE_MORE_FILE => MoreFileComponent::class,
            SellerEnterConfig::TYPE_SELECT => SelectComponent::class,
            SellerEnterConfig::TYPE_RADIO => RadioComponent::class,
            SellerEnterConfig::TYPE_CHECKBOX => CheckboxComponent::class,
            SellerEnterConfig::TYPE_DATE => DateComponent::class,
            SellerEnterConfig::TYPE_TEXTAREA => TextareaComponent::class,
        ];
        if (!isset($components[$component_name])) {
            throw new BusinessException('没有找到' . $component_name . '组件！');
        }
        $class = $components[$component_name];
        $page_component = new $class($component_name, $name);
        //实例化组件
        if (!$page_component instanceof PageComponent) {
            throw new BusinessException($class . ' is not instance of PageComponent');
        }
        return $page_component;
    }

    /**
     * get PageComponent by name
     * @param string $alias
     * @param string $name component name | param option
     * @return PageComponent
     * @throws BusinessException
     */
    public static function getComponent(string $alias, string $name = ''): PageComponent
    {
        //define components
        $components = [
            AppWebsiteDecorationItem::COMPONENT_NAME_LABEL => LabelColumnComponent::class, // 标签栏
            AppWebsiteDecorationItem::COMPONENT_NAME_CHANNEL_SQUARE => ChannelSquareComponent::class, // 频道广场 -
            AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV => HomeNavColumnComponent::class, // 导航栏组件
            AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK => QuickLinkComponent::class, // 金刚区
            AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN => AdvertComponent::class, // 大屏广告
            AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING => AdvertComponent::class, // 侧边广告位
            AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT => AdvertComponent::class, // 二楼广告位
            AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE => AdvertisingComponent::class, // 广告位1
            AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO => AdvertisingComponent::class, // 广告位2
            AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE => AdvertisingThreeComponent::class, // 广告位3
            AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING => ThemeAdvertisingComponent::class, // 主题广告
            // 我的
            AppWebsiteDecorationItem::COMPONENT_NAME_ORDER_CENTER => OrderCenterComponent::class, // 我的-订单中心 -
            AppWebsiteDecorationItem::COMPONENT_NAME_MY_ASSET => MyAssetComponent::class, // 我的-我的资产 -
            AppWebsiteDecorationItem::COMPONENT_NAME_MINE_CUSTOM => MineCustomComponent::class, // 我的-自定义板块 -
            //为您推荐
            AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME => RecommendComponent::class,// 标题+主题
            AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT => RecommendComponent::class,// 标题居左
            AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER => RecommendComponent::class,// 推荐分类
            // 常买常逛
            AppWebsiteDecorationItem::COMPONENT_NAME_BUY_AND_SELL => BuyAndSellComponent::class, // 常买常逛 -
            // 推荐店铺
            AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SELLER => RecommendSellerComponent::class, // 推荐商家 -
            //公共组件
            AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD => HotSaleGoodComponent::class, // 热销商品
        ];
        if (!isset($components[$alias])) {
            throw new BusinessException('没有找到' . $alias . '组件！');
        }
        $class = $components[$alias];
        $page_component = new $class($alias, $name);
        //实例化组件
        if (!$page_component instanceof PageComponent) {
            throw new BusinessException($class . ' is not instance of PageComponent');
        }
        return $page_component;
    }

}
