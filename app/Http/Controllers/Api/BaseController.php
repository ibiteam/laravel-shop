<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;

    final public function user(): ?User
    {
        return request()->user('sanctum');
    }
}
