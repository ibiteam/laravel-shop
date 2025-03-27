<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use ApiResponse;

    final public function user(): ?User
    {
        return Auth::guard('api')->user();
    }
}
