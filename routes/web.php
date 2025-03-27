<?php

use Illuminate\Support\Facades\Route;

//Route::middleware('manage')->prefix(config('app.manage_prefix'))->group(base_path('routes/manage.php'));

//home
Route::get(config('app.manage_prefix').'/{any?}', function () {
    return view('manage');
})->where('any', '^(?!api|storage).*$');

