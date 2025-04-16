<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\User;
use App\Rules\PhoneRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $is_show = $request->get('is_show', -1);
        $number = (int) $request->get('number', 10);
        $list = User::query()->latest()
            ->when($user_name = $request->get('user_name'), fn (Builder $query) => $query->whereLike('user_name', "%{$user_name}%"))
            ->when($is_show >= 0, fn (Builder $query) => $query->where('is_show', $is_show))
            ->orderByDesc('id')
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'user_name' => 'required',
                'phone' => [ 'required', new PhoneRule ],
                'avatar' => 'required|string',
                'password' => [
                    function ($attribute, $value, $fail) use ($request) {
                        if (!($request->id ?? 0)) {
                            if (empty($value)) {
                                $fail('密码不能为空');
                            } elseif (strlen($value) < 6) {
                                $fail('密码长度不能小于6个字符');
                            }
                        } else {
                            if ($value && strlen($value) < 6) {
                                $fail('密码长度不能小于6个字符');
                            }
                        }
                    },
                ],
            ], [], [
                'id' => '分类ID',
                'user_name' => '用户名称',
                'phone' => '手机号',
                'password' => '密码',
                'avatar' => '头像',
            ]);
            $operation = '添加';
            $operation_type = AdminOperationLog::TYPE_STORE;
            if ($id = $validated['id']) {
                $user = User::query()->find($id);

                if (! $user instanceof User) {
                    throw new BusinessException('用户不存在');
                }

                $operation = '修改';
                $operation_type = AdminOperationLog::TYPE_UPDATE;
            } else {
                $user = new User();
                $user->nickname = $validated['user_name'];
            }
            $user->id = $id;
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }
            $user->user_name = $validated['user_name'];
            $user->phone = $validated['phone'];
            $user->avatar = $validated['avatar'];
            $user->register_ip = '';
            $user->source = 'manage';
            if (! $user->save()) {
                throw new BusinessException('保存失败');
            }
            // 记录日志
            admin_operation_log( "{$operation}了用户:{$user->user_name}[{$user->id}]", $operation_type);

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('保存失败');
        }
    }
}
