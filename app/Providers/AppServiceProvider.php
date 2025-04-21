<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 网站颜色
        Model::shouldBeStrict(!is_pro_env());
        if (is_pro_env()) {
            URL::forceScheme('https');
        }

        Validator::extend('is_phone', function ($attribute, $value, $parameters, $validator) {
            return is_phone($value);
        });
    }
}
