<?php
/**
 * created by phpstorm
 * date: 2023/10/19
 * user: walker.
 */

namespace App\Components;

use App\Models\AppWebsiteDecoration;
use App\Models\AppWebsiteDecorationItem;
use App\Models\ZixunAppWebsiteDecorationItem;

class PageDefaultDict
{
    /**
     * 获取移动端装修页面所需要的组件数据.
     *
     * @return array
     */
    public function commonMap(string $alias = '')
    {
        list($component_icon, $component_value, $not_items_fixed_value) = $this->defaultAndFixedComponent($this->getComNameByAlias($alias));

        return [$component_icon, $component_value, $not_items_fixed_value];
    }

    /**
     * 根据别名获取对应组件名.
     *
     * @return array[]
     */
    private function getComNameByAlias(string $alias)
    {
        switch ($alias) {
            case AppWebsiteDecoration::ALIAS_HOME:
                $data = [
                    'advertisement_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING,
                        AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK,
                        AppWebsiteDecorationItem::COMPONENT_NAME_BRAND_CHOICE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_CHANNEL_SQUARE
                    ], // 广告组件
                    'data_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_NEWS,
                        AppWebsiteDecorationItem::COMPONENT_NAME_HOT_LIST,
                        AppWebsiteDecorationItem::COMPONENT_NAME_FLASH_SALE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SELLER,
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME,
                        AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD,
                    ], // 数据组件
                    'fixed_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_HOME_NAV,
                        AppWebsiteDecorationItem::COMPONENT_NAME_LABEL,
                        AppWebsiteDecorationItem::COMPONENT_NAME_LARGE_SCREEN,
                        AppWebsiteDecorationItem::COMPONENT_NAME_RED_ENVELOPE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
                        AppWebsiteDecorationItem::COMPONENT_NAME_SECOND_ADVERTISEMENT,
                    ], // 固定组件
                ];

                break;
            case AppWebsiteDecoration::ALIAS_MINE:

                $data = [
                    'advertisement_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE], // 广告组件
                    'data_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_MINE_CUSTOM,AppWebsiteDecorationItem::COMPONENT_NAME_BUY_AND_SELL,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT], // 数据组件
                    'fixed_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_ORDER_CENTER, AppWebsiteDecorationItem::COMPONENT_NAME_MY_ASSET], // 固定组件
                ];

                break;
            case AppWebsiteDecoration::ALIAS_INDUSTRIAL:
                $data = [
                    // 广告组件
                    'advertisement_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE, // 广告1
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO, // 广告2
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE, // 广告3
                        AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING, // 主题广告
                        AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK, // 金刚区
                        AppWebsiteDecorationItem::COMPONENT_NAME_BRAND_CHOICE, // 品牌精选
                        AppWebsiteDecorationItem::COMPONENT_NAME_CHANNEL_SQUARE // 频道广场
                    ],
                    // 数据组件
                    'data_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_NEWS, // 新闻
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CATE, // 推荐分类
                        AppWebsiteDecorationItem::COMPONENT_NAME_HOT_LIST,  // 热力榜
                        AppWebsiteDecorationItem::COMPONENT_NAME_FLASH_SALE, // 限时抢购
						AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD, //热销商品
						AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SELLER,// 推荐商家
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER, // 为你推荐（标题居中）
                    ],
                    // 固定组件
                    'fixed_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING
                    ],
                ];

                break;
            case AppWebsiteDecoration::ALIAS_PUBLIC:
                $data = [
                    // 广告组件
                    'advertisement_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE, // 广告1
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO, // 广告2
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE, // 广告3
                        AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING, // 主题广告
                        AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK, // 金刚区
                        AppWebsiteDecorationItem::COMPONENT_NAME_BRAND_CHOICE, // 品牌精选
                        AppWebsiteDecorationItem::COMPONENT_NAME_CHANNEL_SQUARE // 频道广场
                    ],
                    // 数据组件
                    'data_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_NEWS, // 新闻
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CATE, // 推荐分类
                        AppWebsiteDecorationItem::COMPONENT_NAME_HOT_LIST,  // 热力榜
                        AppWebsiteDecorationItem::COMPONENT_NAME_FLASH_SALE, // 限时抢购
						AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD, //热销商品
						AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SELLER,// 推荐商家
                        AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER, // 为你推荐（标题居中）
                    ],
                    // 固定组件
                    'fixed_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING,
                    ],
                ];

                break;
            case AppWebsiteDecoration::ALIAS_ORDER:
                $data = [
                    'advertisement_name' => [], // 广告组件
                    'data_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_BUY_AND_SELL,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT], // 数据组件
                    'fixed_name' => [], // 固定组件
                ];

                break;
            case AppWebsiteDecoration::ALIAS_GOODS:
                $data = [
                    'advertisement_name' => [], // 广告组件
                    'data_name' => [], // 数据组件
                    'fixed_name' => [
                        AppWebsiteDecorationItem::GOODS_HEADER,
                        AppWebsiteDecorationItem::GOODS_FIXED,
                        AppWebsiteDecorationItem::GOODS_MOVE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_SIDE_ADVERTISING
                    ], // 固定组件
                ];

                break;
            case AppWebsiteDecoration::ALIAS_INDEX:
                $data = [
                    'advertisement_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE, AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO, AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE, AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING, AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_PRODUCT, AppWebsiteDecorationItem::COMPONENT_NAME_QUICK_LINK], // 广告组件
                    'data_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_INDEX, AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_MACRO_FUTURES, AppWebsiteDecorationItem::COMPONENT_NAME_COLLEGE_EXPERT, AppWebsiteDecorationItem::COMPONENT_NAME_VIDEO, AppWebsiteDecorationItem::COMPONENT_NAME_MEETING], // 数据组件
                    'fixed_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_LABEL], // 固定组件
                ];

                break;
            case AppWebsiteDecoration::ALIAS_CATEGORY:
			case AppWebsiteDecoration::ALIAS_CATEGORY_LIST:
				$data = [
                    'advertisement_name' => [], // 广告组件
                    'data_name' => [], // 数据组件
                    'fixed_name' => [AppWebsiteDecorationItem::COMPONENT_NAME_CATEGORY], // 固定组件
                ];

                break;
			case AppWebsiteDecoration::ALIAS_SELLER_HOME: //卖家版首页
				$data = [
					// 广告组件
					'advertisement_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_ADVERTISING_ONE, // 广告1

					],
					// 数据组件
					'data_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_GOODS_DATA, // 商品数据
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_ORDER_DATA, // 订单数据
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_BUSINESS_DATA,  // 经营数据
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_HELP_CENTER, // 帮助中心

					],
					// 固定组件
					'fixed_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_SHOP_INFO,//店铺信息
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_LABEL, //标签栏
					],
				];

				break;
			case AppWebsiteDecoration::ALIAS_SELLER_WORKBENCH: //卖家版工作台
				$data = [
					// 广告组件
					'advertisement_name' => [],
					// 数据组件
					'data_name' => [],
					// 固定组件
					'fixed_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_SELLER_STORE_NAV,//店铺导航
					],
				];

				break;
			case AppWebsiteDecoration::ALIAS_TRY_CENTER: //买家版试用中心
				$data = [
					// 广告组件
					'advertisement_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE, // 广告位

					],
					// 数据组件
					'data_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_TRY_NOTICE,
						AppWebsiteDecorationItem::COMPONENT_NAME_TRY_TITLE,
						AppWebsiteDecorationItem::COMPONENT_NAME_TRY_FREE_GOODS,
						AppWebsiteDecorationItem::COMPONENT_NAME_TRY_CHARGE_GOODS,
					],
					// 固定组件
					'fixed_name' => [
						AppWebsiteDecorationItem::COMPONENT_NAME_TRY_LABEL,
					],
				];

				break;
            case AppWebsiteDecoration::ALIAS_ZIXUN_HOME_PAGE: // 移动端资讯首页
                $data = [
                    // 广告组件
                    'advertisement_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_ONE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_TWO,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ADVERTISING_THREE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_THEME_ADVERTISING,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_QUICK_LINK,// 金刚区
                        AppWebsiteDecorationItem::COMPONENT_NAME_PUBLIC_IMAGE,// 图片样式
                    ],
                    // 数据组件
                    'data_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_VIDEO,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_ARTICLE,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_APP_INDEX,
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_MACRO_FUTURES,
                        AppWebsiteDecorationItem::COMPONENT_NAME_MEETING
                    ],
                    // 固定组件
                    'fixed_name' => [
                        AppWebsiteDecorationItem::COMPONENT_NAME_ZIXUN_LABEL,
                    ],
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
