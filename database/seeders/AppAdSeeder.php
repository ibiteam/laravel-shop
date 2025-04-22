<?php

namespace Database\Seeders;

use App\Models\AdCate;
use Illuminate\Database\Seeder;

class AppAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ad_cate = AdCate::firstOrNew([
            'alias' => AdCate::APP_SHOP_TOP_BANNER,
        ]);

        if (! $ad_cate->exists) {
            $ad_cate->name = 'App超市顶部banner';
            $ad_cate->alias = AdCate::APP_SHOP_TOP_BANNER;
            $ad_cate->type = AdCate::MOBILE_TYPE;
            $ad_cate->save();
        }
    }
}
