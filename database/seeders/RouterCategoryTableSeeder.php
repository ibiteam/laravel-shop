<?php

namespace Database\Seeders;

use App\Enums\RouterCategoryEnum;
use App\Models\Permission;
use App\Models\RouterCategory;
use Illuminate\Database\Seeder;

class RouterCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一级
        $this->addRouterCategory('基础链接', RouterCategoryEnum::BASE_LINK, null, RouterCategory::TYPE_LINK, 100, Permission::MANAGE_ROUTER_INDEX);
        $this->addRouterCategory('商品链接', RouterCategoryEnum::GOODS_LINK, null, RouterCategory::TYPE_MENU, 99);
        $this->addRouterCategory('文章链接', RouterCategoryEnum::ARTICLE_LINK, null, RouterCategory::TYPE_MENU, 98);
        $this->addRouterCategory('自定义链接', RouterCategoryEnum::CUSTOM_LINK, null, RouterCategory::TYPE_LINK, 97, Permission::MANAGE_ROUTER_INDEX);

        // 二级
        $this->addRouterCategory('商品分类', RouterCategoryEnum::GOODS_CATEGORY, RouterCategoryEnum::GOODS_LINK, RouterCategory::TYPE_MENU, 0, Permission::MANAGE_GOODS_INDEX);
        $this->addRouterCategory('商品', RouterCategoryEnum::GOODS_LIST, RouterCategoryEnum::GOODS_LINK, RouterCategory::TYPE_MENU, 0, Permission::MANAGE_CATEGORY_INDEX);
        // $this->addRouterCategory('文章分类', RouterCategoryEnum::ARTICLE_CATEGORY, RouterCategoryEnum::ARTICLE_LINK, RouterCategory::TYPE_MENU, 0, Permission::MANAGE_ARTICLE_INDEX);
        // $this->addRouterCategory('文章', RouterCategoryEnum::ARTICLE_LIST, RouterCategoryEnum::ARTICLE_LINK, RouterCategory::TYPE_MENU, 0, Permission::MANAGE_ARTICLE_CATEGORY_INDEX);
    }

    private function addRouterCategory(
        string $name,
        RouterCategoryEnum $alias,
        ?RouterCategoryEnum $parent_alias,
        int $type,
        int $sort = 0,
        string $page_name = ''
    ): void {
        if ($parent_alias) {
            $parent_category = RouterCategory::query()->whereAlias($parent_alias->value)->first();

            if (! $parent_category) {
                return;
            }
            $parent_id = $parent_category->id;
        } else {
            $parent_id = 0;
        }

        if ($parent_id && ! $page_name) {
            // 存在父级，则设置page_name
            return;
        }

        if (! $parent_id && $type == RouterCategory::TYPE_MENU) {
            // 不存在父级，菜单类型不设置page_name
            $page_name = '';
        }

        $router_category = RouterCategory::query()->firstOrNew(['alias' => $alias->value]);

        if (! $router_category->exists) {
            $router_category->parent_id = $parent_id;
            $router_category->alias = $alias->value;
            $router_category->name = $name;
            $router_category->type = $type;
            $router_category->page_name = $page_name;
            $router_category->sort = $sort;
            $router_category->is_show = RouterCategory::IS_SHOW_YES;
            $router_category->save();
        }
    }
}
