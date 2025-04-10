<?php

namespace App\Http\Dao;

use App\Models\Permission;
use App\Models\AccessRecord;

class AccessRecordDao
{
    /**
     * 增加/更新 最近访问记录.
     */
    public function updateOrCreate(int $admin_user_id, ?string $name): void
    {
        if ($admin_user_id <= 0 || !$name) {
            return;
        }

        $permission = Permission::query()
            ->where('parent_id', '>', 0)
            ->whereIsLeftNav(Permission::IS_LEFT_NAV)
            ->where('name', $name)->first();
        if (!$permission) {
            return;
        }

        AccessRecord::updateOrCreate(['admin_user_id' => $admin_user_id, 'permission_id' => $permission->id], ['updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * 获取最新访问记录.
     */
    public function getListByAdminUserId(int $admin_user_id, int $limit = 12)
    {
        $permission_ids = AccessRecord::query()
            ->whereHas('permission')
            ->whereAdminUserId($admin_user_id)
            ->whereIsShow(AccessRecord::IS_SHOW_YES)
            ->orderByDesc('updated_at')
            ->pluck('permission_id')->toArray();
        if (empty($permission_ids)) {
            return [];
        }

        return Permission::query()
            ->whereIsLeftNav(Permission::IS_LEFT_NAV)
            ->whereIn('id', $permission_ids)
            ->orderByRaw('FIND_IN_SET(id,?)', [implode(',', $permission_ids)])
            ->limit($limit)
            ->get()->map(function (Permission $permission) {
                return [
                    'id' => $permission->id,
                    'title' => $permission->display_name,
                    'name' => $permission->name,
                    'icon' => $permission->icon,
                ];
            })->toArray();
    }
}
