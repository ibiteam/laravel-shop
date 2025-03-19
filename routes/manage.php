<?php

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\WorkbenchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('manage.login.form');
Route::post('login', [LoginController::class, 'login'])->name('manage.login.password');

Route::middleware('manage.auth')->group(function () {
    Route::get('home', [WorkbenchController::class, 'index'])->name('manage.home');
});
