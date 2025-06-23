<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ConstantEnum;
use App\Enums\PhoneMsgTypeEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Rules\PhoneRule;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class SmsController extends BaseController
{
    public function handleAction(Request $request, SmsService $sms_service)
    {
        try {
            $current_user = get_user();
            $need_phone_actions = [
                SmsService::ACTION_LOGIN,
                SmsService::ACTION_FORGET_PASSWORD,
                SmsService::ACTION_EDIT_PHONE,
            ];
            $validated = $request->validate([
                'action' => [
                    'required',
                    'string',
                    'in:'.implode(',', [
                        SmsService::ACTION_LOGIN,
                        SmsService::ACTION_FORGET_PASSWORD,
                        SmsService::ACTION_EDIT_PASSWORD,
                        SmsService::ACTION_VERIFY_PHONE,
                        SmsService::ACTION_EDIT_PHONE,
                    ]),
                ],
                'phone' => [
                    'required_if:action,'.implode(',', $need_phone_actions), 'integer', new PhoneRule,
                ],
            ], [], [
                'action' => '操作类型',
                'phone' => '手机号',
            ]);

            if (in_array($validated['action'], $need_phone_actions)) {
                if ($request->header('phone-verify') != md5($validated['phone'].$validated['action'].$request->get('timestamp'))) {
                    throw new BusinessException('发送失败');
                }
            } else {
                if ($request->header('phone-verify') != md5($validated['action'].$request->get('timestamp'))) {
                    throw new BusinessException('发送失败');
                }
            }

            $sms_service->sendOtp($validated['action'], $validated['phone'] ?? 0, $current_user);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('发送失败');
        }

        return $this->success('短信发送成功');
    }

    // 验证手机号验证码
    public function checkActionCode(Request $request, SmsService $sms_service)
    {
        try {
            $validated = $request->validate([
                'action' => [
                    'required',
                    'string',
                    'in:'.implode(',', [
                        SmsService::ACTION_VERIFY_PHONE,
                    ]),
                ],
                'code' => 'required|string',
            ], [], [
                'action' => '操作类型',
                'code' => '验证码',
            ]);
            $user = get_user();

            if (! $user) {
                throw new BusinessException('用户未登录', ConstantEnum::UNAUTHORIZED);
            }

            if (! $sms_service->verifyOtp($user->phone, $validated['code'], PhoneMsgTypeEnum::ACTION_VERIFY_PHONE)) {
                throw new BusinessException('验证码输入错误');
            }
            $data['token'] = Cache::put("{$user->id}_{$validated['action']}_{$user->phone}", md5($user->phone.time()), now()->addMinutes(5));

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('验证失败');
        }
    }
}
