<?php

namespace App\Http\Dao;

use App\Enums\CacheNameEnum;
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
            return Cache::remember(CacheNameEnum::SHOP_CONFIG_ALL->value, Carbon::now()->endOfDay(), function () {
                return ShopConfig::query()->get()->mapWithKeys(fn (ShopConfig $shop_config) => [$shop_config->code => $shop_config->value]);
            });
        }

        return Cache::rememberForever(CacheNameEnum::SHOP_CONFIG_ALL->value, function () {
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

    /**
     * 获取指定配置 返回code=>value.
     */
    public function getConfigByCodes(array $codes): array
    {
        return ShopConfig::whereIn('code', $codes)->pluck('value', 'code')->toArray();
    }

    /**
     * 获取指定配置 返回code=>value.
     */
    public function getConfigByGroupName(string $group_name): array
    {
        return ShopConfig::whereGroupName($group_name)->pluck('value', 'code')->toArray();
    }
}
