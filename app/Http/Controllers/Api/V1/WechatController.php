<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Models\WechatUser;
use App\Services\UserService;
use App\Utils\Wechat\WechatOfficialAccountUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Overtrue\Socialite\Contracts\UserInterface;

class WechatController extends BaseController
{
    /**
     * 微信网页版授权.
     */
    public function auth(Request $request, WechatOfficialAccountUtil $wechat_official_account_util, UserService $user_service): JsonResponse
    {
        // 微信网页版授权
        try {
            $validated = $request->validate([
                'code' => 'required|string',
            ], [], [
                'code' => '授权码',
            ]);
            $official_account_user = $wechat_official_account_util->getUserFromCode($validated['code']);

            if (! $official_account_user instanceof UserInterface || ! $official_account_user->getId()) {
                return $this->error('user not fount');
            }
            // 获取用户信息 查询 wechat_user 表
            $wechat_user = WechatUser::query()->with('user')->firstOrNew(['openid' => $official_account_user->getId()]);
            $wechat_user->nickname = $official_account_user->getNickname();
            $wechat_user->avatar = $official_account_user->getAvatar();
            $wechat_raw = $official_account_user->getRaw();

            if (isset($wechat_raw['unionid'])) {
                $wechat_user->unionid = $wechat_raw['unionid'];
            }
            $wechat_user->is_subscribe = (bool) ($wechat_raw['subscribe'] ?? 0);
            $wechat_user->subscribe_time = $wechat_raw['subscribe_time'] ?? null;
            $wechat_user->language = $wechat_raw['language'] ?? 'zh_CN';
            $wechat_user->remark = $wechat_raw['remark'] ?? '';

            $wechat_user->save();

            $user = $wechat_user->user;
            $response = [
                'token' => '',
                'openid' => $wechat_user->openid,
            ];

            if ($user instanceof User) {
                $response['token'] = $user_service->loginSuccess($user);
            }

            return $this->success($response);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
