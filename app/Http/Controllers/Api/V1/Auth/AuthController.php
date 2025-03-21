<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\UserDao;
use App\Models\User;
use App\Rules\PhoneRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function checkPhone(Request $request, UserDao $user_dao): JsonResponse
    {
        try {
            $validated = $request->validate(['phone' => ['required', 'integer', new PhoneRule]], [], ['phone' => '手机号']);
            $user = $user_dao->getInfoByPhone($validated['phone']);

            return $this->success([
                'is_register' => $user instanceof User,
            ]);
        } catch (\Throwable $throwable) {
            return $this->error('请求失败');
        }
    }

    public function checkLogin(Request $request): JsonResponse
    {
        $user = $this->user();

        return $this->success(['is_login' => $user instanceof User]);
    }
}
