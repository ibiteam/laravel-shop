<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\ShopConfigDao;
use App\Models\AdminUser;
use App\Models\ShopConfig;
use App\Rules\CaptchaRule;
use App\Utils\RsaUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    use AuthenticatesUsers;

    public function username()
    {
        return 'user_name';
    }

    public function showLoginForm(Request $request, ShopConfigDao $shop_config_dao)
    {
        $current_user = $this->guard()->user();

        if ($current_user instanceof AdminUser) {
            return redirect()->to($this->redirectTo());
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

        $can_password_login = true;

        $rsa_public_key = shop_config(ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '');
        $other_login_methods = [
            [
                'name' => '企业微信',
                'can_use' => true,
                'icon' => url('/images/manage/login-work-wechat.png'),
                'class' => 'work_wechat',
                'redirect' => '',
            ],
            [
                'name' => '钉钉',
                'can_use' => true,
                'icon' => url('/images/manage/login-ding-talk.png'),
                'class' => 'ding_talk',
                'redirect' => '',
            ],
        ];

        return view('manage.login-password', compact('config', 'other_login_methods', 'rsa_public_key', 'can_password_login'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));

            return $this->error(trans('auth.throttle', ['seconds' => $seconds]));
        }
        $validated = $this->credentials($request);

        $admin_user = AdminUser::where('user_name', $validated[$this->username()])->first();

        if (! $admin_user instanceof AdminUser) {
            return $this->error('用户名或密码错误');
        }

        if (! password_verify(RsaUtil::getDecodeData($validated['password']), $admin_user->password)) {
            $this->incrementLoginAttempts($request);

            return $this->error('用户名或密码错误~');
        }

        if ($admin_user->status !== AdminUser::STATUS_ENABLE) {
            return $this->error('该用户已被禁用');
        }

        $this->guard()->login($admin_user);

        if ($request->hasSession()) {
            $request->session()->put('auth.password_confirmed_at', time());
        }
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->success(['redirect' => $this->redirectTo()]);
    }

    protected function validateLogin(Request $request)
    {
        $validate = config('custom.net_east_yi_dun.enable') ? 'required' : 'sometimes';
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'validate' => [
                $validate, new CaptchaRule,
            ],
        ]);
    }

    protected function guard()
    {
        return Auth::guard('manage');
    }

    /**
     * 登录成功跳转路由设置.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('manage.home');
    }
}
