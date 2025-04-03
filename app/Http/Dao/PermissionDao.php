<?php

namespace App\Http\Dao;

use App\Models\AdminUser;
use App\Models\Permission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission as SpatiePermission;

class PermissionDao
{
    /**
     * 根据用户获取对应的菜单权限.
     */
    public function getTreePermissionByAdminUser(AdminUser $admin_user, array $collect_permission_ids = []): array
    {
        $guard_name = config('auth.manage.guard') ?: 'manage';
        $cache_key = 'permission_menus_'.$admin_user->id;

        try {
            // 缓存逻辑
            $menus = Cache::remember(
                $cache_key,
                is_local_env() ? Carbon::now()->endOfDay() : null, // 本地环境缓存1天，其他环境永久缓存
                function () use ($admin_user, $guard_name, $collect_permission_ids) {
                    return $this->fetchAndFormatPermissions($admin_user, $guard_name, $collect_permission_ids);
                }
            );
        } catch (\Exception $e) {
            // 异常处理：如果缓存操作失败，回退到直接查询数据库
            $menus = $this->fetchAndFormatPermissions($admin_user, $guard_name, $collect_permission_ids);
        }

        // 构建树结构并返回
        return $this->buildTree($menus, 'index');
    }

    /**
     * 查询并格式化权限数据.
     */
    private function fetchAndFormatPermissions(AdminUser $admin_user, string $guard_name, array $collect_permission_ids): array
    {
        return $admin_user->getPermissionsViaRoles()
            ->where('is_left_nav', Permission::IS_LEFT_NAV)
            ->where('guard_name', $guard_name)
            ->unique('id')
            ->values()
            ->map(function (SpatiePermission $permission) use ($collect_permission_ids) {
                return [
                    'index' => (string) $permission->id,
                    'parent_id' => $permission->parent_id,
                    'name' => $permission->name,
                    'title' => $permission->display_name,
                    'icon' => $permission->icon,
                    'sort' => $permission->sort,
                    'is_collection' => isset($collect_permission_ids[$permission->id]),
                ];
            })
            ->toArray();
    }

    private function buildTree(array $data, string $primary_key = 'id', int $parent_id = 0, int $level = 0): array
    {
        $response = [];

        foreach ($data as $item) {
            if ($item['parent_id'] == $parent_id) {
                $item['level'] = $level;
                $children = $this->buildTree($data, $primary_key, $item[$primary_key], $level + 1);

                if (! empty($children)) {
                    $temp_sort_key = array_column($children, 'sort');
                    array_multisort($temp_sort_key, SORT_DESC, $children);
                    $item['children'] = $children;
                }
                $response[] = $item;
            }
        }
        $sort_key = array_column($response, 'sort');
        array_multisort($sort_key, SORT_DESC, $response);

        return $response;
    }
}
