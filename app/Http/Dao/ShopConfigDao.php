<?php

namespace App\Http\Dao;

use App\Models\ShopConfig;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ShopConfigDao
{
    /**
     * 从缓存中获取所有配置.
     */
    public function getAll(): mixed
    {
        if (is_local_env()) {
            return Cache::remember('shop_config_all_code', Carbon::now()->endOfDay(), function () {
                return ShopConfig::query()->get()->mapWithKeys(fn (ShopConfig $shop_config) => [$shop_config->code => $shop_config->value]);
            });
        }

        return Cache::rememberForever('shop_config_all_code', function () {
            return ShopConfig::query()->get()->mapWithKeys(fn (ShopConfig $shop_config) => [$shop_config->code => $shop_config->value]);
        });
    }

    /**
     * 获取配置.
     */
    public function config(string $code, mixed $default = null): mixed
    {
        return $this->getAll()->get($code, $default);
    }

    /**
     * 批量获取配置.
     */
    public function multipleConfig(...$codes): Collection
    {
        $all_config = $this->getAll();
        $collection = collect();

        foreach ($codes as $code) {
            $collection->put($code, $all_config->get($code, null));
        }

        return $collection;
    }
}
