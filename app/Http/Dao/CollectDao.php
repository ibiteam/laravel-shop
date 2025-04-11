<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\AdminUser;
use App\Models\Collect;
use App\Models\Permission;

class CollectDao
{
    /**
     * 增加/删除 收藏菜单.
     *
     * @throws BusinessException
     */
    public function createOrDelete(AdminUser $admin_user, int $permission_id): bool
    {
        $permission = Permission::query()->whereId($permission_id)->first();

        if (! $permission) {
            throw new BusinessException('权限不存在');
        }

        $permission_codes = $admin_user->getAllPermissions()->pluck('name')->flip();

        if (! isset($permission_codes[$permission->name])) {
            throw new BusinessException('没有权限');
        }

        $collect = Collect::query()->whereAdminUserId($admin_user->id)->wherePermissionId($permission_id)->first();

        if ($collect) {
            if (! $collect->delete()) {
                throw new BusinessException('取消收藏失败');
            }
        } else {
            $collect = Collect::create([
                'admin_user_id' => $admin_user->id,
                'permission_id' => $permission_id,
            ]);

            if (! $collect) {
                throw new BusinessException('收藏失败');
            }
        }

        return true;
    }

    /**
     * 获取收藏记录.
     */
    public function getListByAdminUserId(int $admin_user_id, int $limit = 0): array
    {
        $permission_ids = Collect::query()
            ->whereHas('permission')
            ->whereAdminUserId($admin_user_id)
            ->orderByDesc('updated_at')
            ->pluck('permission_id')->toArray();

        if (empty($permission_ids)) {
            return [];
        }

        return Permission::query()
            ->whereIn('id', $permission_ids)
            ->orderByRaw('FIND_IN_SET(id,?)', [implode(',', $permission_ids)])
            ->when($limit, function ($query, $limit) {
                return $query->limit($limit);
            })->get()->map(function (Permission $permission) {
                return [
                    'id' => $permission->id,
                    'title' => $permission->display_name,
                    'name' => $permission->name,
                    'icon' => $permission->icon,
                ];
            })->toArray();
    }
}
