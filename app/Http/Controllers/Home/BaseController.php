<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    use ApiResponse;

    final public function user()
    {
        return request()->user('sanctum');
    }
}
