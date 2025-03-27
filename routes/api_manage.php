<?php

use App\Http\Controllers\Manage\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('manage.login');
Route::post('login', [LoginController::class, 'login'])->name('manage.login.submit');
