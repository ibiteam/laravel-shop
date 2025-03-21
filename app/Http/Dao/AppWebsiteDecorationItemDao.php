<?php

namespace App\Http\Dao;

use App\Components\ComponentFactory;
use App\Models\AppWebsiteDecoration;
use App\Models\AppWebsiteDecorationItem;
use Illuminate\Support\Facades\Cache;

class AppWebsiteDecorationItemDao
{
    public function model()
    {
        return AppWebsiteDecorationItem::class;
    }

    /**
     * 装修商品详情配置信息.
     */
    public function goodsConfig(): array
    {
        return Cache::rememberForever(AppWebsiteDecoration::MOBILE_GOODS_CONFIG, function () {
            $items = AppWebsiteDecoration::whereAlias(AppWebsiteDecoration::ALIAS_GOODS)->with('item')->first()->item ?? [];
            $config = [];

            foreach ($items as $item) {
                $config[$item['component_name']] = $item['content'];
            }

            return $config;
        });
    }

    /**
     * 获取组件属性名称.
     */
    public function goodsItemConfig($componentName, $itemName): array
    {
        if (! in_array($componentName, [AppWebsiteDecorationItem::GOODS_FIXED, AppWebsiteDecorationItem::GOODS_HEADER, AppWebsiteDecorationItem::GOODS_MOVE])) {
            return [];
        }
        $componentConfig = $this->goodsConfig()[$componentName] ?? [];

        if (empty($componentConfig)) {
            return [];
        }

        if ($componentName === AppWebsiteDecorationItem::GOODS_MOVE) {
            foreach ($componentConfig as $item) {
                if ($item['name'] === $itemName) {
                    return $item;
                }
            }
        } elseif ($componentName === AppWebsiteDecorationItem::GOODS_FIXED) {
            return $componentConfig[$itemName];
        }

        return [];
    }

    /**
     * 获取试用商品
     *
     * @param int $item_id  装修栏目id
     * @param int $cat_name 分类名称
     */
    public function chargeTryByItemCatId(int $item_id = 0, array $content = [], $cat_name = ''): array
    {
        $item = AppWebsiteDecorationItem::whereId($item_id)->whereComponentName(AppWebsiteDecorationItem::COMPONENT_NAME_TRY_CHARGE_GOODS)->first();

        if (! $item && ! $content) {
            return [];
        }

        $component_name = $item->component_name ?? AppWebsiteDecorationItem::COMPONENT_NAME_TRY_CHARGE_GOODS;
        $content = $content ?: ($item->content ?? []);

        try {
            $component = ComponentFactory::getComponent($component_name, '');

            return $component->getGoodsInfo($content, $cat_name);
        } catch (\Exception $exception) {
            if ($exception instanceof \App\Exceptions\BusinessException) {
                return [];
            }

            return [];
        }
    }

    /**
     * 获取试样商品
     *
     * @param int $item_id  装修栏目id
     * @param int $cat_name 分类名称
     */
    public function freeTryByItemCatId(int $item_id = 0, array $content = [], $cat_name = ''): array
    {
        $item = AppWebsiteDecorationItem::whereId($item_id)->whereComponentName(AppWebsiteDecorationItem::COMPONENT_NAME_TRY_FREE_GOODS)->first();

        if (! $item && ! $content) {
            return [];
        }

        $component_name = $item->component_name ?? AppWebsiteDecorationItem::COMPONENT_NAME_TRY_FREE_GOODS;
        $content = $content ?: ($item->content ?? []);

        try {
            $component = ComponentFactory::getComponent($component_name, '');

            return $component->getGoodsInfo($content, $cat_name);
        } catch (\Exception $exception) {
            if ($exception instanceof \App\Exceptions\BusinessException) {
                return [];
            }

            return [];
        }
    }

    /**
     * 获取热销商品
     *
     * @param int $item_id 装修栏目id
     * @param int $cat_id  分类id
     * @param int $user_id 用户id
     */
    public function hotGoodByItemCatId(int $item_id = 0, array $content = [], int $cat_id = 0, int $user_id = 0): array
    {
        $item = AppWebsiteDecorationItem::whereId($item_id)->whereComponentName(AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD)->first();

        if (! $item && ! $content) {
            return [];
        }

        $component_name = $item->component_name ?? AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD;
        $content = $content ?: ($item->content ?? []);

        try {
            $component = ComponentFactory::getComponent($component_name, '');
            $data = $component->getGoodInfo($content, $cat_id, $user_id);

            return $data;
        } catch (\Exception $exception) {
            if ($exception instanceof \App\Exceptions\BusinessException) {
                return [];
            }

            return [];
        }
    }

    /**
     * 获取展示样式为静态列表的新闻分类+文章数据
     * 切换一级分类 获取所有二级分类+第一个二级下的文章.
     *
     * @param int   $item_id        装修栏目id
     * @param array $content        未保存前用content(总后台装修页）
     * @param int   $primary_cat_id 一级分类id
     */
    public function secondCatsNews(int $item_id = 0, array $content = [], int $primary_cat_id = 0): array
    {
        $result = [
            'second_class' => [],
            'article_data' => [],
        ];
        $item = AppWebsiteDecorationItem::whereId($item_id)->whereComponentName(AppWebsiteDecorationItem::COMPONENT_NAME_NEWS)->first();

        if (! $item && ! $content) {
            return $result;
        }
        $component_name = $item->component_name ?? AppWebsiteDecorationItem::COMPONENT_NAME_NEWS;
        $carousel_items = $content ? ($content['carousel_items'] ?? []) : ($item->content['carousel_items'] ?? []);

        try {
            $component = ComponentFactory::getComponent($component_name, '');

            return $component->secondCategoryNews($carousel_items, $primary_cat_id);
        } catch (\Exception $exception) {
            if ($exception instanceof \App\Exceptions\BusinessException) {
                return $result;
            }

            return $result;
        }
    }
}
