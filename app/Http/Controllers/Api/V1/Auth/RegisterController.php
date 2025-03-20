<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\PhoneMsg;
use App\Models\User;
use App\Rules\PhoneRule;
use App\Services\SmsService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterController extends BaseController
{
    public function registerByPhone(Request $request, SmsService $sms_service, UserService $user_service)
    {
        try {
            $validated = $request->validate([
                'phone' => [
                    'required',
                    'integer',
                    new PhoneRule,
                    Rule::unique((new User)->getTable(), 'phone'),
                ],
                'code' => 'required|string',
            ], [], [
                'phone' => '手机号',
                'code' => '验证码',
            ]);

            if (! $sms_service->verifyOtp($validated['phone'], $validated['code'], PhoneMsg::PHONE_REGISTER)) {
                throw new BusinessException('验证码输入错误');
            }
            $source = get_source();

            $user = $user_service->registerByPhone($validated['phone'], $source);

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
}
