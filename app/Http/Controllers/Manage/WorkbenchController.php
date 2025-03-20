<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\PermissionDao;
use Illuminate\Http\Request;

class WorkbenchController extends BaseController
{
    public function index(Request $request, PermissionDao $permissionDao)
    {
        $admin_user = $request->user();
        $menus = $permissionDao->getTreePermissionByAdminUser($admin_user);

        return view('manage.home.index', compact('admin_user', 'menus'));
    }
}
