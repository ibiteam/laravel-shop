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
        $this->addRouterCategory(RouterCategoryEnum::BASE_LINK, '基础链接', RouterCategory::TYPE_LINK, Permission::MANAGE_ROUTER_INDEX, 100);
        $this->addRouterCategory(RouterCategoryEnum::CUSTOM_LINK, '自定义链接', RouterCategory::TYPE_LINK, Permission::MANAGE_ROUTER_INDEX);
        $this->addRouterCategory(RouterCategoryEnum::GOODS_LINK, '商品链接', RouterCategory::TYPE_MENU, '', 99);
    }

    private function addRouterCategory(
        RouterCategoryEnum $router_category_enum,
        string $name,
        int $type,
        string $page_name,
        int $sort = 0
    ): void {
        $router_category = RouterCategory::query()->firstOrNew(['alias' => $router_category_enum->value]);

        if ($type == RouterCategory::TYPE_MENU) {
            $page_name = '';
        }

        if (! $router_category->exists) {
            $router_category->parent_id = 0;
            $router_category->alias = $router_category_enum->value;
            $router_category->name = $name;
            $router_category->type = $type;
            $router_category->page_name = $page_name;
            $router_category->sort = $sort;
            $router_category->is_show = RouterCategory::IS_SHOW_YES;
            $router_category->save();
        }
    }
}
