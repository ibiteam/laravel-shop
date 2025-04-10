<?php

namespace App\Components;

use App\Exceptions\BusinessException;
use App\Models\AppDecorationItem;
use App\Models\AppWebsiteDecoration;

class PageDefaultDict
{
    /**
     * 获取移动端装修页面所需要的组件数据.
     *
     * @return array
     */
    public function commonMap(string $alias = ''): array
    {
        list($component_icon, $component_value, $not_items_fixed_value) = $this->defaultAndFixedComponent($this->getComNameByAlias($alias));

        return [$component_icon, $component_value, $not_items_fixed_value];
    }

    /**
     * 根据别名获取对应组件名.
     *
     * @return array[]
     */
    private function getComNameByAlias(string $alias): array
    {
        switch ($alias) {
            case AppWebsiteDecoration::ALIAS_HOME:
                $data = [
                    // 广告组件
                    'advertisement_name' => [
                        AppDecorationItem::COMPONENT_NAME_QUICK_LINK,
                        AppDecorationItem::COMPONENT_NAME_HOT_ZONE,
                        AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL,
                        AppDecorationItem::COMPONENT_NAME_ADVERTISING_BANNER,
                    ],
                    // 数据组件
                    'data_name' => [
                        AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND,
                        AppDecorationItem::COMPONENT_NAME_RECOMMEND,
                    ], // 固定组件
                    'fixed_name' => [
                        AppDecorationItem::COMPONENT_NAME_HOME_NAV,
                        AppDecorationItem::COMPONENT_NAME_LABEL,
                        AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
                        AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
                    ]
                ];

                break;
            default:
                $data = [
                    'advertisement_name' => [], // 广告组件
                    'data_name' => [], // 数据组件
                    'fixed_name' => [], // 固定组件
                ];
        }
        return $data;
    }

    /**
     * 首页默认组件以及固定组件.
     *
     * @return array
     * @throws BusinessException
     */
    private function defaultAndFixedComponent($names)
    {
        $advertisement_component_icon = [];
        $data_component_icon = [];
        $component_value = [];
        /* 广告组件 */
        foreach ($names['advertisement_name'] ?? [] as $default_icon) {
            $component = ComponentFactory::getComponent($default_icon);
            $advertisement_component_icon[] = $component->icon();
            $component_value[] = $component->parameter();
        }
        /* 数据组件 */
        foreach ($names['data_name'] ?? [] as $default_icon) {
            $component = ComponentFactory::getComponent($default_icon);
            $data_component_icon[] = $component->icon();
            $component_value[] = $component->parameter();
        }
        $component_icon = [
            'advertisement_component' => $advertisement_component_icon,
            'data_component' => $data_component_icon,
        ];
        /* 固定组件 */
        $not_items_fixed_value = [];
        foreach ($names['fixed_name'] ?? [] as $default_icon) {
            $component = ComponentFactory::getComponent($default_icon);
            $not_items_fixed_value[] = $component->display([]);
        }

        return [$component_icon, $component_value, $not_items_fixed_value];
    }
}
