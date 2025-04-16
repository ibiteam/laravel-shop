<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use ApiResponse;
}
