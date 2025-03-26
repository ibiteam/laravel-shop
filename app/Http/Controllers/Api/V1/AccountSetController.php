<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\UserDao;
use App\Models\PhoneMsg;
use App\Models\User;
use App\Rules\PhoneRule;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountSetController extends BaseController
{
    protected $user;

    public function __construct(Request $request)
    {
        $this->user = $request->user();
    }

    // 获取用户信息
    public function getUserInfo()
    {
        $user = $this->user;
        $data['user_name'] = $user->user_name;
        $data['nickname'] = $user->nickname;
        $data['phone'] = $user->phone;
        $data['avatar'] = $user->avatar;
        $data['is_modify'] = $user->is_modify;

        return $this->success($data);
    }

    // 设置用户名
    public function setUserName(Request $request)
    {
        $user = $this->user;
        $user_id = $user->user_id;
        $user_name = $request->input('user_name');

        if (get_sensitive_words($user_name)) {
            return $this->error('抱歉，用户名中包含敏感词，请重新填写');
        }
        $repeat = User::whereUserName($user_name)->where('user_id', '<>', $user_id)->first();

        if ($repeat) {
            return $this->error('该用户名已被注册，请重新输入');
        }

        if ($user->is_modify == User::IS_MODIFY_YES) {
            return $this->error('用户名仅支持修改一次哦');
        }

        if (app(UserDao::class)->checkUserName($user_name)) {
            return $this->error('输入的用户名不能包含特殊字符。');
        }

        // 用户名3-22个字符，支持字母（区分大小写）、数字、下划线组合，不支持以下划线开头
        if (strpos($user_name, '_') === 0) {
            return $this->error('用户名不能以“_”开头');
        }

        if (preg_match('/^[\d_]+$/', $user_name)) {
            return $this->error('用户名不能设置为纯数字');
        }

        if (! preg_match('/^(?=.*[a-zA-Z])[a-zA-Z0-9]\w{2,21}$/', $user_name)) {
            return $this->error('用户名3-22个字符，支持字母（区分大小写）、数字、下划线，不支持以下划线开头');
        }
        $user->user_name = $user_name;
        $user->is_modify = User::IS_MODIFY_YES;

        if ($user->save()) {
            return $this->success('保存成功');
        }

        return $this->error('保存失败');
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
        if (!$this->user->save()) {
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
            if (! $sms_service->verifyOtp($phone, $validated['code'], PhoneMsg::PHONE_LOGIN)) {
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
