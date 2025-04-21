<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\IndexRequest;
use App\Http\Requests\Manage\UpdateRequest;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 管理员列表.
 */
class AdminUserController extends BaseController
{
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
            $admin_user->role_name = $role_names;
            $admin_user->role_ids = $role_ids;
            $admin_user->latest_login_time = $latest_login_time;

            return $admin_user;
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
    public function update(UpdateRequest $request)
    {
        $id = $request['id'];

        if ($id === 0) {
            $id = null;
        }
        $validated = $request->validate([
            'user_name' => 'required|string|unique:admin_users,user_name,'.$id.',id|max:255',
            'password' => 'nullable|string|confirmed',
            'phone' => 'required|is_phone|unique:admin_users,phone,'.$id.',id',
            'job_no' => 'nullable|string|unique:admin_users,job_no,'.$id.',id',
            'role_ids' => 'required|array',
            'status' => 'required|boolean',
        ], [], [
            'user_name' => '用户名',
            'password' => '登录密码',
            'phone' => '手机号',
            'job_no' => '工号',
            'role_ids' => '所属角色',
            'status' => '状态',
        ]);
        $password = $validated['password'] ?? '';
        $confirm_password = $validated['password_confirmation'] ?? '';

        if ($id) {
            $admin_user = AdminUser::whereId($id)->firstOrFail();
        } else {
            $admin_user = new AdminUser;
        }

        // 密码处理
        if ($password) {
            if ($password != $confirm_password) {
                return $this->error('登录密码和确认密码不一致');
            }

            if (! preg_match('/^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/', $confirm_password)) {
                return $this->error('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种');
            }
            $admin_user->password = $password;
        }

        $admin_user->user_name = $validated['user_name'];
        $admin_user->job_no = $validated['job_no'] ?? '';
        $admin_user->phone = $validated['phone'];
        $admin_user->status = (int) $validated['status'];

        if (! $admin_user->save()) {
            return $this->error('保存失败');
        }

        // 角色处理
        $old_roles = $admin_user->roles->pluck('id')->toArray();
        $role_info = Role::whereIn('id', $validated['role_ids'])->get();

        if ($role_info->isNotEmpty()) {
            $admin_user->syncRoles($role_info);
        }
        $new_roles = $admin_user->roles->pluck('id')->toArray();

        if ($old_roles && $old_roles !== $new_roles) {
            $role_names = $role_info->map(function (Role $item) {
                return $item->display_name;
            })->toArray();

            $role_log = "更新管理员[id:{$admin_user->id}]角色为[".implode(',', $role_names).']';
            admin_operation_log($role_log);
        }

        if ($id) {
            $log = "编辑管理员[id:{$admin_user->id}]".implode(',', array_map(function ($k, $v) {
                return sprintf('%s=`%s`', $k, $v);
            }, array_keys($admin_user->getChanges()), $admin_user->getChanges()));
            admin_operation_log($log);
        } else {
            $log = "新增管理员[id:{$admin_user->id}]";
            admin_operation_log($log, AdminOperationLog::TYPE_STORE);
        }

        return $this->success('保存成功');
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
