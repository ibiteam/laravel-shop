<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;

    final public function user(): ?User
    {
        $user = request()->user('sanctum');

        if ($user && ! $user->seller_id && isset($user->sellerEnter->sellerShop)) {
            $user->seller_id = $user->sellerEnter->sellerShop->seller_id;
        }

        return $user;
    }
}
