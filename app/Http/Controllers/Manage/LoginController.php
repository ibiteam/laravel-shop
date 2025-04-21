<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\AdminUserLoginLogDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\AdminUser;
use App\Models\AdminUserLoginLog;
use App\Models\ShopConfig;
use App\Services\AdminUserService;
use App\Utils\RsaUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
{
    /**
     * 登录初始化页面.
     */
    public function showLoginForm(Request $request, ShopConfigDao $shop_config_dao): JsonResponse|RedirectResponse
    {
        $current_user = get_admin_user();

        if ($current_user instanceof AdminUser) {
            return $this->success(['is_login' => true, 'config' => []]);
        }

        $config = $shop_config_dao->multipleConfig(
            ShopConfig::SHOP_NAME,
            ShopConfig::SHOP_KEYWORDS,
            ShopConfig::SHOP_DESCRIPTION,
            ShopConfig::SHOP_ICON,
            ShopConfig::SHOP_LOGO,
            ShopConfig::SHOP_MANAGE_LOGIN_IMAGE,
            ShopConfig::BANK_ACCOUNT,
            ShopConfig::SHOP_ADDRESS,
            ShopConfig::SERVICE_MOBILE,
            ShopConfig::ICP_NUMBER,
        );

        return $this->success(['is_login' => false, 'config' => $config]);
    }

    public function login(Request $request, AdminUserService $admin_user_service)
    {
        try {
            $validated = $request->validate([
                'user_name' => 'required|string',
                'password' => 'required|string',
            ], [], [
                'user_name' => '用户名',
                'password' => '密码',
            ]);
            $admin_user = AdminUser::query()->where('user_name', $validated['user_name'])->first();

            if (! $admin_user instanceof AdminUser) {
                throw new BusinessException('用户名或密码错误');
            }

            $tmp_password = shop_config(ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '') ? RsaUtil::getDecodeData($validated['password']) : $validated['password'];

            if (! password_verify($tmp_password, $admin_user->password)) {
                app(AdminUserLoginLogDao::class)->addLoginLogByAdminUser(
                    $admin_user,
                    AdminUserLoginLog::TYPE_PASSWORD,
                    AdminUserLoginLog::STATUS_FAILED,
                    '账号密码登录失败：密码错误。'
                );

                throw new BusinessException('用户名或密码错误~');
            }

            if ($admin_user->status !== AdminUser::STATUS_ENABLE) {
                throw new BusinessException('该用户已被禁用');
            }

            return $this->success($admin_user_service->loginSuccess($admin_user));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('登录失败');
        }
    }

    /**
     * 退出登录.
     */
    public function logout()
    {
        try {
            $admin_user = get_admin_user();

            if (! $admin_user instanceof AdminUser) {
                throw new BusinessException('用户未登录');
            }

            $admin_user->currentAccessToken()->delete();

            return $this->success('退出成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('退出登录异常');
        }
    }
}
