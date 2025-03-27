<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Services\ManageRouteService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use ApiResponse;

    final public function adminUser(): ?AdminUser
    {
        return Auth::guard(config('auth.manage.guard'))->user();
    }

    /**
     * 跳转后台首页设置.
     */
    protected function redirectToHome(): string
    {
        return ManageRouteService::manageHome();
    }
}
