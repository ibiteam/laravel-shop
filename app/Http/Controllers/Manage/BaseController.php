<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;

    final public function adminUser(): ?AdminUser
    {
        return request()->user(config('auth.manage.guard'));
    }
}
