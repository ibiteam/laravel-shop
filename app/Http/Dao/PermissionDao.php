<?php

namespace App\Http\Dao;

use App\Models\AdminUser;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission as SpatiePermission;

class PermissionDao
{
    public function model()
    {
        return Permission::class;
    }

    /**
     * 根据用户获取对应的菜单权限.
     */
    public function getTreePermissionByAdminUser(AdminUser $admin_user, array $collection = []): array
    {
        $guard_name = config('auth.manage.guard') ?: 'manage';
        $menus = $admin_user->getPermissionsViaRoles()
            ->where('is_left_nav', Permission::IS_LEFT_NAV)
            ->where('guard_name', $guard_name)->unique('id')->values()->map(function (SpatiePermission $permission) use ($collection) {
                return [
                    'index' => (string) $permission->id,
                    'is_collection' => isset($collection[$permission->id]),
                    'parent_id' => $permission->parent_id,
                    'name' => $permission->name,
                    'title' => $permission->display_name,
                    'icon' => $permission->icon,
                    'src' => $permission->parent_id && Route::has($permission->name) ? route($permission->name) : '',
                    'sort' => $permission->sort,
                ];
            })->toArray();

        return $this->build_tree($menus, 'index');
    }

    // 获取菜单树
    public function allData($keywords = '')
    {
        $query = Permission::orderByDesc('sort')->orderBy('id');

        if ($keywords) {
            $query->where('name', 'like', "%{$keywords}%")
                ->orWhere('display_name', 'like', "%{$keywords}%");
        }

        $data = $query->get()->toArray();

        //没有进行搜索的话就显示菜单树
        if (empty($keywords) && $data) {
            $data = $this->get_tree($data);
        }

        return $data;
    }

    public function selfData($permission_ids)
    {
        $data = Permission::whereIn('id', $permission_ids)
            ->orderByDesc('sort')
            ->orderBy('id')
            ->select(['id', 'display_name', 'parent_id'])
            ->get()
            ->toArray();

        if ($data) {
            $data = $this->get_tree($data);
            $data = $this->dataProcessing($data, []);
        }

        return $data;
    }

    private function build_tree(array $data, string $primary_key = 'id', int $parent_id = 0, int $level = 0)
    {
        $response = [];

        foreach ($data as $item) {
            if ($item['parent_id'] == $parent_id) {
                $item['level'] = $level;
                $children = $this->build_tree($data, $primary_key, $item[$primary_key], $level + 1);

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
        foreach ($data as $v) {
            if (isset($v['children']) && $v['children']) {
                $permission_ids = $this->dataProcessing($v['children'], $permission_ids);
            } else {
                $permission_ids[] = $v['id'];
            }
        }

        return $permission_ids;
    }

    // 数据处理
    private function get_tree($arr)
    {
        $refer = $tree = [];

        foreach ($arr as $k => $v) {
            $refer[$v['id']] = &$arr[$k];  //创建主键的数组引用
        }

        foreach ($arr as $k => $v) {
            $pid = $v['parent_id'];   //获取当前分类的父级id

            if ($pid == 0) {
                $tree[] = &$arr[$k];    //顶级栏目
            } else {
                if (isset($refer[$pid])) {
                    $refer[$pid]['children'][] = &$arr[$k];    //如果存在父级栏目，则添加进父级栏目的子栏目数组中
                }
            }
        }

        return $tree;
    }
}
