<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\ShopConfig;
use App\Utils\RsaUtil;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 管理员列表.
 */
class AdminUserController extends BaseController
{
    // 列表
    public function index(Request $request)
    {
        $user_name = $request->get('user_name');
        $role_id = $request->get('role_id', '');
        $status = $request->get('status', '1');
        $number = (int) $request->get('number', 10);

        $data = AdminUser::with(['modelHasRole.role'])->orderByDesc('id')
            ->when($user_name, fn ($query) => $query->where('user_name', 'like', '%'.$user_name.'%'))
            ->when($status > -1, fn ($query) => $query->where('status', '=', $status))
            ->when($role_id, fn ($query) => $query->whereHas('modelHasRole', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            }))
            ->paginate($number);
        $data->getCollection()->transform(function (AdminUser $admin_user) {

            $role_names = $admin_user->modelHasRole->filter(function ($modelHasRole) {
                return $modelHasRole->role && $modelHasRole->role->is_show === Role::SHOW;
            })->map(function ($modelHasRole) {
                return $modelHasRole->role->display_name ?? '';
            })->implode(',');

            $role_ids = $admin_user->modelHasRole->pluck('role_id')->toArray();

            return [
                'id' => $admin_user->id,
                'user_name' => $admin_user->user_name,
                'phone' => $admin_user->phone,
                'role_name' => $role_names,
                'role_ids' => $role_ids,
                'status' => $admin_user->status,
                'created_at' => $admin_user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $admin_user->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 角色.
     */
    public function roles()
    {
        $roles = Role::whereIsShow(Role::SHOW)->get()->map(function ($item) {
            return [
                'value' => (int) $item->id,
                'label' => $item->display_name,
            ];
        })->toArray();

        return $this->success($roles);
    }

    /**
     * 添加/编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'user_name' => 'required|string',
                'password' => 'nullable|string',
                'confirm_password' => 'nullable|string',
                'phone' => 'required|is_phone',
                'role_ids' => 'required|array',
                'status' => 'required|boolean',
            ], [], [
                'id' => '管理员ID',
                'user_name' => '用户名',
                'password' => '密码',
                'confirm_password' => '确认密码',
                'phone' => '手机号',
                'role_ids' => '所属角色',
                'status' => '状态',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $admin_user = AdminUser::whereId($validated['id'])->first();

                if (! $admin_user) {
                    throw new BusinessException('管理员不存在');
                }

                if (AdminUser::where('id', '!=', $validated['id'])->whereUserName($validated['user_name'])->first()) {
                    throw new BusinessException('管理员用户名已存在');
                }
            } else {
                $admin_user = new AdminUser;

                if (AdminUser::whereUserName($validated['user_name'])->first()) {
                    throw new BusinessException('管理员用户名已存在');
                }
            }

            // 密码处理
            $password = $validated['password'] ?? '';
            $confirm_password = $validated['confirm_password'] ?? '';

            if ($password) {
                if ($password != $confirm_password) {
                    throw new BusinessException('密码和确认密码不一致');
                }

                if (! preg_match('/^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/', $confirm_password)) {
                    throw new BusinessException('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种');
                }

                // $tmp_password = shop_config(ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '') ? RsaUtil::getDecodeData($password) : $password;
                $admin_user->password = $password;
            }

            $admin_user->user_name = $validated['user_name'];
            $admin_user->nickname = '';
            $admin_user->avatar = '';
            $admin_user->phone = $validated['phone'];
            $admin_user->status = intval($validated['status']);

            if (! $admin_user->save()) {
                throw new BusinessException('保存失败');
            }

            // 角色处理
            $role_info = Role::whereIn('id', $validated['role_ids'])->get();

            if ($role_info->isNotEmpty()) {
                $admin_user->syncRoles($role_info);
            }

            if ($validated['id']) {
                $log = "编辑管理员[id:{$admin_user->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($admin_user->getChanges()), $admin_user->getChanges()));
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增管理员[id:{$admin_user->id}]";
                admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_STORE);
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('管理员操作异常~'.$throwable->getMessage());
        }
    }

    /**
     * 切换状态.
     */
    public function changeStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|integer|in:0,1',
            ], [], [
                'id' => '管理员ID',
                'status' => '状态',
            ]);

            $admin_user = AdminUser::query()->whereId($validated['id'])->first();

            if (! $admin_user) {
                throw new BusinessException('管理员不存在');
            }
            $admin_user->status = $validated['status'];

            if (! $admin_user->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改管理员状态[id:{$validated['id']}]".implode(',', array_map(function ($k, $v) {
                return sprintf('%s=`%s`', $k, $v);
            }, array_keys($admin_user->getChanges()), $admin_user->getChanges()));
            admin_operation_log($this->adminUser(), $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换状态异常~');
        }
    }
}
