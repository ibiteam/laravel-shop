<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Rules\PhoneRule;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SmsController extends BaseController
{
    public function handleAction(Request $request, SmsService $sms_service)
    {
        try {
            $validated = $request->validate([
                'action' => [
                    'required',
                    'string',
                    'in:'.implode(',', [
                        SmsService::ACTION_LOGIN,
                        SmsService::ACTION_REGISTER,
                        SmsService::ACTION_FORGET_PASSWORD,
                        SmsService::ACTION_EDIT_PASSWORD,
                    ]),
                ],
                'phone' => [
                    'required_if:action,'.implode(',', [
                        SmsService::ACTION_LOGIN,
                        SmsService::ACTION_REGISTER,
                        SmsService::ACTION_FORGET_PASSWORD,
                    ]), 'integer', new PhoneRule,
                ],
            ], [], [
                'action' => '操作类型',
                'phone' => '手机号',
            ]);

            $sms_service->sendOtp($validated['action'], $validated['phone'] ?? 0, $this->user());
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('发送失败');
        }

        return $this->success('短信发送成功');
    }
}
