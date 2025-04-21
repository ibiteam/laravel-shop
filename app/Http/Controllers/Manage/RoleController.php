<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\PermissionDao;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\RoleHasPermission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 角色管理.
 */
class RoleController extends BaseController
{
    public function index(Request $request)
    {
        $display_name = $request->get('display_name', '');
        $number = (int) $request->get('number', 10);

        $data = Role::query()->orderByDesc('id')
            ->withCount(['modelHasRole' => function ($query) {
                $query->whereHas('adminUser', function ($q) {
                    $q->where('status', 1);
                });
            }])
            ->when($display_name, fn ($query) => $query->where('display_name', 'like', '%'.$display_name.'%'))
            ->paginate($number);
        $data->getCollection()->transform(function (Role $role) {
            $role_number = ($role->is_show == Role::SHOW) ? $role->model_has_role_count : 0;

            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'role_number' => $role_number,
                'is_show' => $role->is_show,
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 获取信息.
     */
    public function info(Request $request, PermissionDao $permission_dao)
    {
        $id = $request->get('id') ?? 0;

        $role_info = [
            'id' => 0,
            'display_name' => '',
            'description' => '',
            'self_permissions' => [],
        ];

        if ($id) {
            $info = Role::query()->whereId($id)->select(['id', 'display_name', 'description'])->first();

            if (! $info instanceof Role) {
                return $this->error('数据不存在');
            }

            $role_info['id'] = $info['id'];
            $role_info['display_name'] = $info['display_name'];
            $role_info['description'] = $info['description'];
            $permission_ids = RoleHasPermission::where('role_id', $id)->pluck('permission_id')->toArray();
            $role_info['self_permissions'] = $permission_dao->selfData($permission_ids);
        }

        $all_permissions = $permission_dao->allData();

        return $this->success([
            'all_permissions' => $all_permissions,
            'role_info' => $role_info,
        ]);
    }

    /**
     * 添加|编辑.
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'display_name' => 'required|string',
                'description' => 'nullable|string',
                'self_permissions' => 'required|array',
            ], [], [
                'id' => '角色ID',
                'display_name' => '角色名称',
                'description' => '角色描述',
                'self_permissions' => '角色权限',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $role = Role::whereId($validated['id'])->first();

                if (! $role) {
                    throw new BusinessException('角色不存在');
                }

                if (Role::where('id', '!=', $validated['id'])->whereDisplayName($validated['display_name'])->first()) {
                    throw new BusinessException('角色名称已存在');
                }
            } else {
                $role = new Role;

                if (Role::whereDisplayName($validated['display_name'])->first()) {
                    throw new BusinessException('角色名称已存在');
                }
            }

            $role->name = md5(time());
            $role->guard_name = $role->guardName();
            $role->display_name = $validated['display_name'];
            $role->description = $validated['description'];

            if (! $role->save()) {
                throw new BusinessException('保存失败');
            }

            // 先删除角色的权限
            RoleHasPermission::whereRoleId($validated['id'])->delete();
            // 插入权限
            $role_permission_data = [];

            foreach ($validated['self_permissions'] as $self_permission) {
                $role_permission_data[] = [
                    'permission_id' => $self_permission,
                    'role_id' => $role->id,
                ];
            }
            RoleHasPermission::query()->insert($role_permission_data);

            if ($validated['id']) {
                $log = "编辑角色[id:{$role->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($role->getChanges()), $role->getChanges()));
                admin_operation_log( $log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增角色[id:{$role->id}]";
                admin_operation_log( $log, AdminOperationLog::TYPE_STORE);
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('角色操作异常~');
        }
    }

    /**
     * 删除.
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '角色ID',
            ]);
            $role = Role::query()->whereId($validated['id'])->first();

            if (! $role instanceof Role) {
                throw new BusinessException('角色不存在');
            }

            if (ModelHasRole::whereRoleId($validated['id'])->count()) {
                throw new BusinessException('该角色底下有用户，不可删除');
            }

            if ($role->delete()) {
                // 删除角色的权限
                RoleHasPermission::whereRoleId($validated['id'])->delete();

                // 记录日志
                admin_operation_log( "删除了角色:{$role->display_name}[{$role->id}]", AdminOperationLog::TYPE_DESTROY);

                return $this->success('删除成功');
            }

            throw new BusinessException('删除失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('删除异常');
        }
    }

    /**
     * 切换是否显示.
     */
    public function changeShow(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'is_show' => 'required|integer|in:0,1',
            ], [], [
                'id' => '角色ID',
                'is_show' => '是否显示',
            ]);

            $role = Role::query()->whereId($validated['id'])->first();

            if (! $role) {
                throw new BusinessException('角色不存在');
            }

            $role->is_show = $validated['is_show'];

            if (! $role->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改角色显示隐藏[id:{$validated['id']}]".implode(
                ',',
                array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($role->getChanges()), $role->getChanges())
            );
            admin_operation_log( $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换是否展示异常~');
        }
    }
}
