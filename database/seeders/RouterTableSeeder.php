<?php

namespace Database\Seeders;

use App\Enums\RouterCategoryEnum;
use App\Enums\RouterEnum;
use App\Models\Router;
use App\Models\RouterCategory;
use Illuminate\Database\Seeder;

class RouterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 基础链接
        $this->addRouter(RouterCategoryEnum::BASE_LINK, RouterEnum::PAY_SUCCESS, '支付成功', '/pay/success', ['no' => ''], 1);
        $this->addRouter(RouterCategoryEnum::BASE_LINK, RouterEnum::ORDER_SUCCESS, '下单成功', '/order/success', sort: 1);
    }

    private function addRouter(
        RouterCategoryEnum $router_category_alias,
        RouterEnum $router_alias,
        string $name,
        string $h5_url,
        array $params = [],
        int $sort = 0
    ): void {
        $router = Router::query()->firstOrNew(['alias' => $router_alias->value]);

        if (! $router->exists) {
            if ($params && array_values($params) === $params) {
                return; // 参数必须是键值对
            }

            $router_category = RouterCategory::query()->where('alias', $router_category_alias->value)->first();

            if (! $router_category) {
                return;
            }

            $router->router_category_id = $router_category->id;
            $router->alias = $router_alias->value;
            $router->name = $name;
            $router->h5_url = $h5_url;
            $router->params = $params ?: null;
            $router->sort = $sort;
            $router->is_show = Router::IS_SHOW_YES;
            $router->save();
        }
    }
}
