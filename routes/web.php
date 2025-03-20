<?php

use Illuminate\Support\Facades\Route;

Route::middleware('manage')->prefix(config('app.manage_prefix'))->group(base_path('routes/manage.php'));

//home
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

