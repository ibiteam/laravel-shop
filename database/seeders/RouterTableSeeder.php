<?php

namespace Database\Seeders;

use App\Enums\RouteEnum;
use App\Enums\RouterCategoryEnum;
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
        $this->addRouter(RouterCategoryEnum::BASE_LINK, RouteEnum::PAY_SUCCESS, '支付成功', 'https://shop.host/api/pay_success', ['no' => '2023101012345678'], 1);
    }

    private function addRouter(
        RouterCategoryEnum $router_category_enum,
        RouteEnum $router_enum,
        string $name,
        string $h5_url,
        array $params = [],
        int $sort = 0
    ): void {
        $router = Router::query()->firstOrNew(['alias' => $router_enum->value]);

        if (! $router->exists) {
            // 参数必须是键值对
            if ($params && array_values($params) === $params) {
                return;
            }

            $router_category = RouterCategory::query()->where('alias', $router_category_enum->value)->first();

            if (! $router_category) {
                return;
            }

            $router->router_category_id = $router_category->id;
            $router->alias = $router_enum->value;
            $router->name = $name;
            $router->h5_url = $h5_url;
            $router->params = $params ?: null;
            $router->sort = $sort;
            $router->is_show = Router::IS_SHOW_YES;
            $router->save();
        }
    }
}
