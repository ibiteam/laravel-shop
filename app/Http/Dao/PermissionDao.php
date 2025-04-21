<?php

namespace App\Http\Dao;

use App\Enums\CacheNameEnum;
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
        try {
            $menus = Cache::remember(
                CacheNameEnum::ADMIN_PERMISSION_MENUS->value.'_'.$admin_user->id,
                is_local_env() ? Carbon::now()->endOfDay() : null, // 本地环境缓存1天，其他环境永久缓存
                function () use ($admin_user, $collect_permission_ids) {
                    return $this->fetchAndFormatPermissions($admin_user, $collect_permission_ids);
                }
            );
        } catch (\Exception $e) {
            // 异常处理：如果缓存操作失败，回退到直接查询数据库
            $menus = $this->fetchAndFormatPermissions($admin_user, $collect_permission_ids);
        }

        return $this->buildTree($menus, 'index');
    }

    /**
     * 获取所有权限数据.
     */
    public function allData($keywords = ''): array
    {
        $query = Permission::orderByDesc('sort')->orderBy('id');

        if ($keywords) {
            $query->where('display_name', 'like', "%{$keywords}%")->orWhere('name', 'like', "%{$keywords}%");
        }

        $data = $query->get()->toArray();

        // 没有进行搜索的话就显示菜单树
        if (empty($keywords) && $data) {
            $data = $this->get_tree($data);
        }

        return $data;
    }

    /**
     * 获取自己权限数据.
     */
    public function selfData($permission_ids)
    {
        $data = Permission::whereIn('id', $permission_ids)
            ->orderByDesc('sort')->orderBy('id')
            ->select(['id', 'display_name', 'parent_id'])
            ->get()->toArray();

        if ($data) {
            $data = $this->get_tree($data);
            $data = $this->dataProcessing($data, []);
        }

        return $data;
    }

    /**
     * 查询并格式化权限数据.
     */
    public function fetchAndFormatPermissions(AdminUser $admin_user, array $collect_permission_ids): array
    {
        return $admin_user->getPermissionsViaRoles()
            ->where('is_left_nav', Permission::IS_LEFT_NAV)
            ->where('guard_name', config('auth.manage.guard'))
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
                    'is_collection' => in_array($permission->id, $collect_permission_ids),
                ];
            })
            ->toArray();
    }

    public function buildTree(array $data, string $primary_key = 'id', int $parent_id = 0, int $level = 0): array
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

    private function dataProcessing($data, $permission_ids)
    {
        foreach ($data as $key => $v) {
            if (isset($v['children']) && $v['children']) {
                $permission_ids = $this->dataProcessing($v['children'], $permission_ids);
            } else {
                $permission_ids[] = $v['id'];
            }
        }

        return $permission_ids;
    }

    private function get_tree($arr): array
    {
        $refer = $tree = [];

        foreach ($arr as $k => $v) {
            $refer[$v['id']] = &$arr[$k];  // 创建主键的数组引用
        }

        foreach ($arr as $k => $v) {
            $pid = $v['parent_id'];   // 获取当前分类的父级id

            if ($pid == 0) {
                $tree[] = &$arr[$k];    // 顶级栏目
            } else {
                if (isset($refer[$pid])) {
                    $refer[$pid]['children'][] = &$arr[$k];    // 如果存在父级栏目，则添加进父级栏目的子栏目数组中
                }
            }
        }

        return $tree;
    }
}
