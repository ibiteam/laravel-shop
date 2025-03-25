<?php

namespace App\Http\Controllers\Home;

use App\Enums\CustomCodeEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\UserDao;
use App\Http\Dao\UserLogDao;
use App\Models\PhoneMsg;
use App\Models\User;
use App\Models\UserLog;
use App\Rules\PhoneRule;
use App\Services\PasswordRuleService;
use App\Services\SmsService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * 检测是否登录.
     */
    public function checkLogin(Request $request, UserService $user_service): JsonResponse
    {
        return $this->success($user_service->checkIsLogin($this->user(), $request->bearerToken()));
    }

    /**
     * 检测用户名是否注册.
     */
    public function checkUserName(Request $request, UserDao $user_dao): JsonResponse
    {
        try {
            $validated = $request->validate(['account' => 'required|string'], [], ['account' => '用户名']);

            $user = $user_dao->getInfoByUserName($validated['account']);

            if ($user instanceof User) {
                throw new BusinessException('该用户名已注册');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('校验失败');
        }

        return $this->success([]);
    }

    /**
     * 检测手机号是否注册.
     *
     * @return void
     */
    public function checkPhone(Request $request, UserDao $user_dao): JsonResponse
    {
        try {
            $validated = $request->validate(['phone' => 'required|integer'], [], ['phone' => '手机号']);

            $user = $user_dao->getInfoByPhone($validated['phone']);

            if ($user instanceof User) {
                throw new BusinessException('该手机号已注册');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('校验失败');
        }

        return $this->success([]);
    }

    /**
     * 用户注册.
     */
    public function register(Request $request, UserDao $user_dao, SmsService $sms_service, UserService $user_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'account' => 'required|string',
                'password' => ['required', 'string', 'confirmed', PasswordRuleService::userPasswordRule()],
                'phone' => ['required', 'integer', new PhoneRule],
                'code' => 'required|string',
                'agreement' => 'required|accepted'
            ], [], [
                'account' => '用户名',
                'password' => '密码',
                'password_confirmation' => '确认密码',
                'phone' => '手机号',
                'code' => '验证码',
                'agreement' => '协议'
            ]);

            $user = $user_dao->getInfoByUserName($validated['account']);

            if ($user instanceof User) {
                throw new BusinessException('该用户名已注册');
            }
            $user = $user_dao->getInfoByPhone($validated['phone']);

            if ($user instanceof User) {
                throw new BusinessException('该手机号已注册');
            }

            if (! $sms_service->verifyOtp($validated['phone'], $validated['code'], PhoneMsg::PHONE_REGISTER)) {
                throw new BusinessException('验证码输入错误');
            }
            $source = get_source();

            $user = $user_service->registerByUserNameAndPhone($validated, $source);

            $data = $user_service->loginSuccess($user, $source);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('注册失败');
        }

        return $this->success($data);
    }

    /**
     * 手机号登录.
     */
    public function loginByPhone(Request $request, SmsService $sms_service, UserDao $user_dao, UserService $user_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => [
                    'required',
                    'integer',
                    new PhoneRule,
                    Rule::exists((new User)->getTable(), 'phone'),
                ],
                'code' => 'required|string',
            ], [], [
                'phone' => '手机号',
                'code' => '验证码',
            ]);

            if (! $sms_service->verifyOtp($validated['phone'], $validated['code'], PhoneMsg::PHONE_LOGIN)) {
                throw new BusinessException('验证码输入错误');
            }

            $user = $user_dao->getInfoByPhone($validated['phone']);

            if (! $user instanceof User) {
                throw new BusinessException('该手机号未注册');
            }

            $data = $user_service->loginSuccess($user, get_source());
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('登录失败');
        }

        return $this->success($data);
    }

    /**
     * 账号密码登录.
     */
    public function loginByPassword(Request $request, UserDao $user_dao, UserService $user_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'account' => 'required|string',
                'password' => 'required|string',
            ], [], [
                'account' => '账号',
                'password' => '密码',
            ]);

            if (is_phone($validated['account'])) {
                $user = $user_dao->getInfoByPhone($validated['account']);
            } else {
                $user = $user_dao->getInfoByUserName($validated['account']);
            }

            if (! $user instanceof User) {
                throw new BusinessException('账号或密码错误');
            }

            if (! password_verify($validated['password'], $user->password)) {
                throw new BusinessException('账号或密码错误~');
            }

            $data = $user_service->loginSuccess($user, get_source());
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('登录失败');
        }

        return $this->success($data);
    }
}
