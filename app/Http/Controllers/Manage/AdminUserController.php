<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\IndexRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 管理员列表.
 */
class AdminUserController extends BaseController
{
    // 列表
    public function index(IndexRequest $request)
    {
        $user_name = $request->get('user_name');
        $role_id = $request->get('role_id', '');
        $status = $request->get('status', '1');
        $data = AdminUser::with(['modelHasRole.role', 'loginLog'])->orderByDesc('id')
            ->when($user_name, fn ($query) => $query->where('user_name', 'like', '%'.$user_name.'%'))
            ->when($status > -1, fn ($query) => $query->where('status', '=', $status))
            ->when($role_id, fn ($query) => $query->whereHas('modelHasRole', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            }))
            ->paginate($request->per_page);
        $data->getCollection()->transform(function (AdminUser $admin_user) {
            $role_names = $admin_user->modelHasRole->filter(function ($modelHasRole) {
                return $modelHasRole->role && $modelHasRole->role->is_show === Role::SHOW;
            })->map(function ($modelHasRole) {
                return $modelHasRole->role->display_name ?? '';
            })->implode(',');

            $role_ids = $admin_user->modelHasRole->pluck('role_id')->toArray();

            // 最新登录时间
            $login_log = $admin_user->loginLog->last();
            $latest_login_time = $login_log ? $login_log->created_at->format('Y-m-d H:i:s') : '';

            return [
                'id' => $admin_user->id,
                'user_name' => $admin_user->user_name,
                'phone' => $admin_user->phone,
                'job_no' => $admin_user->job_no,
                'role_name' => $role_names,
                'role_ids' => $role_ids,
                'status' => $admin_user->status,
                'latest_login_time' => $latest_login_time,
                'created_at' => $admin_user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success($data);
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
                'job_no' => 'nullable|string',
                'role_ids' => 'required|array',
                'status' => 'required|boolean',
            ], [], [
                'id' => '管理员ID',
                'user_name' => '用户名',
                'password' => '登录密码',
                'confirm_password' => '确认密码',
                'phone' => '手机号',
                'job_no' => '工号',
                'role_ids' => '所属角色',
                'status' => '状态',
            ]);

            $validated['id'] = $validated['id'] ?? 0;
            $password = $validated['password'] ?? '';
            $confirm_password = $validated['confirm_password'] ?? '';
            $job_no = $validated['job_no'] ?? '';

            if ($validated['id']) {
                $admin_user = AdminUser::whereId($validated['id'])->first();

                if (! $admin_user) {
                    throw new BusinessException('管理员不存在');
                }

                if (AdminUser::where('id', '!=', $validated['id'])->whereUserName($validated['user_name'])->first()) {
                    throw new BusinessException('管理员用户名已存在');
                }

                if ($job_no && AdminUser::where('id', '!=', $validated['id'])->whereJobNo($job_no)->first()) {
                    throw new BusinessException('管理员工号已存在');
                }
            } else {
                $admin_user = new AdminUser;

                if (AdminUser::whereUserName($validated['user_name'])->first()) {
                    throw new BusinessException('管理员用户名已存在');
                }

                if ($job_no && AdminUser::whereJobNo($job_no)->first()) {
                    throw new BusinessException('管理员工号已存在');
                }
            }

            // 密码处理
            if ($password) {
                if ($password != $confirm_password) {
                    throw new BusinessException('登录密码和确认密码不一致');
                }

                if (! preg_match('/^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/', $confirm_password)) {
                    throw new BusinessException('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种');
                }

                $admin_user->password = $password;
            }

            $admin_user->user_name = $validated['user_name'];
            $admin_user->job_no = $job_no;
            $admin_user->phone = $validated['phone'];
            $admin_user->status = intval($validated['status']);

            if (! $admin_user->save()) {
                throw new BusinessException('保存失败');
            }

            // 角色处理
            $old_roles = $admin_user->roles->pluck('id')->toArray();
            $role_info = Role::whereIn('id', $validated['role_ids'])->get();

            if ($role_info->isNotEmpty()) {
                $admin_user->syncRoles($role_info);
            }
            $new_roles = $admin_user->roles->pluck('id')->toArray();

            if ($old_roles && $old_roles != $new_roles) {
                $role_names = $role_info->map(function (Role $item) {
                    return $item->display_name;
                })->toArray();

                $role_log = "更新管理员[id:{$admin_user->id}]角色为[".implode(',', $role_names).']';
                admin_operation_log($role_log, AdminOperationLog::TYPE_UPDATE);
            }

            if ($validated['id']) {
                $log = "编辑管理员[id:{$admin_user->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($admin_user->getChanges()), $admin_user->getChanges()));
                admin_operation_log($log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增管理员[id:{$admin_user->id}]";
                admin_operation_log($log, AdminOperationLog::TYPE_STORE);
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
     * 修改字段.
     */
    public function changeField(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'field' => 'required|string|in:status',
        ], [], [
            'id' => 'ID',
            'name' => '名称',
            'field' => '字段',
        ]);
        $admin_user = AdminUser::findOrFail($validated['id']);
        $field = (string) $validated['field'];
        $value = ! $admin_user->$field;
        $status = $value ? '启用' : '禁用';
        $message = '修改了管理员id='.$admin_user->id.'的'.$validated['name'].'['.$field.']变更为'.$status;
        $admin_user->$field = $value;

        if (! $admin_user->save()) {
            return $this->error('修改失败');
        }
        admin_operation_log($message);

        return $this->success('修改成功');
    }
}
