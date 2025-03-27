<?php

use Illuminate\Support\Facades\Route;

Route::get(config('app.manage_prefix').'/{any?}', function () {
    return view('manage');
})->where('any', '^(?!api|storage).*$');

