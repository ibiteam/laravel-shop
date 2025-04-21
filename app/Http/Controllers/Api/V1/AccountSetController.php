<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PhoneMsgTypeEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\OrderEvaluate;
use App\Models\User;
use App\Rules\PhoneRule;
use App\Rules\UserNameRule;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountSetController extends BaseController
{
    protected $user;

    public function __construct()
    {
        $this->user = get_user();
    }

    // 获取用户信息
    public function getUserInfo()
    {
        $user = $this->user;
        $data = [];
        if ($user) {
            $evaluate_ids = OrderEvaluate::query()->whereUserId($user->id)->pluck('order_id')->unique()->filter()->toArray();
            $data['user_name'] = $user->user_name;
            $data['nickname'] = $user->nickname;
            $data['phone'] = $user->phone;
            $data['avatar'] = $user->avatar;
            $data['is_modify'] = $user->is_modify;
            $data['wait_pay_count'] = Order::query()->whereUserId($user->id)->searchWaitPay()->count();
            $data['wait_ship_count'] = Order::query()->whereUserId($user->id)->searchWaitShip()->count();
            $data['wait_evaluate_count'] = Order::query()->whereUserId($user->id)->searchWaitEvaluate($evaluate_ids)->count();
        }

        return $this->success($data);
    }

    // 设置用户名
    public function setUserName(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_name' => [
                    'required',
                    'string',
                    new UserNameRule,
                    Rule::unique((new User)
                        ->getTable(), 'user_name')
                        ->ignore($this->user->id),
                ],
            ], [
                'user_name.unique' => '该用户名已被注册，请重新填写',
            ], [
                'user_name' => '用户名',
            ]);
            $user_name = $validated['user_name'];

            if (get_sensitive_words($user_name)) {
                return $this->error('抱歉，用户名中包含敏感词，请重新填写');
            }

            if ($this->user->is_modify == User::IS_MODIFY_YES) {
                return $this->error('用户名仅支持修改一次哦');
            }
            $this->user->user_name = $user_name;
            $this->user->is_modify = User::IS_MODIFY_YES;

            if (! $this->user->save()) {
                throw new BusinessException('保存失败');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }

    // 设置用户昵称
    public function setNickname(Request $request)
    {
        try {
            $validated = $request->validate([
                'nickname' => [
                    'required',
                    'string',
                    Rule::unique((new User)
                        ->getTable(), 'nickname')
                        ->ignore($this->user->id),
                ],
            ], [
                'nickname.unique' => '该昵称已被绑定，请重新填写',
            ], [
                'nickname' => '昵称',
            ]);
            $nickname = $validated['nickname'];

            if (get_sensitive_words($nickname)) {
                return $this->error('抱歉，昵称中包含敏感词，请重新填写');
            }

            $this->user->nickname = $nickname;

            if (! $this->user->save()) {
                throw new BusinessException('保存失败');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }

    // 设置用户头像
    public function setUserAvatar(Request $request)
    {
        $avatar = $request->get('avatar');

        if (! $avatar) {
            return $this->error('头像不能为空');
        }
        $this->user->avatar = $avatar;

        if (! $this->user->save()) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }

    // 修改注册手机号
    public function setUserPhone(Request $request, SmsService $sms_service)
    {
        try {
            $validated = $request->validate([
                'phone' => [
                    'required',
                    'integer',
                    new PhoneRule,
                    Rule::unique((new User)
                        ->getTable(), 'phone')
                        ->ignore($this->user->id),
                ],
                'code' => 'required|string',
            ], [
                'phone.unique' => '该手机号码已被绑定，请修改成其他手机',
            ], [
                'phone' => '手机号',
                'code' => '验证码',
            ]);
            $phone = $validated['phone'];

            if (! $sms_service->verifyOtp($phone, $validated['code'], PhoneMsgTypeEnum::PHONE_EDIT)) {
                throw new BusinessException('验证码输入错误');
            }

            if ($this->user->phone == $phone) {
                throw new BusinessException('修改的手机号不能与原手机号一致，请修改成其他手机');
            }

            $this->user->phone = $phone;

            if (! $this->user->save()) {
                throw new BusinessException('保存失败');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }
}
