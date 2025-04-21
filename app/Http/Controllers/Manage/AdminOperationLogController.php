<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * 管理员日志.
 */
class AdminOperationLogController extends BaseController
{
    public function index(Request $request)
    {
        $admin_user_name = $request->get('admin_user_name', '');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $description = $request->get('description', '');
        $role_id = $request->get('role_id', '');
        $ip = $request->get('ip', '');
        $number = (int) $request->get('number', 10);

        $query = AdminOperationLog::query()->with(['adminUser', 'modelHasRole.role'])
            ->orderByDesc('created_at')->orderByDesc('id');

        if ($start_time) {
            $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($start_time)));
        }

        if ($end_time) {
            $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($end_time)));
        }

        if ($admin_user_name) {
            $query->whereHas('adminUser', function ($query) use ($admin_user_name) {
                $query->where('user_name', 'like', "%{$admin_user_name}%");
            });
        }

        if ($role_id) {
            $query->whereHas('modelHasRole', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            });
        }

        if ($description) {
            $query->where('description', 'like', "%{$description}%");
        }

        if ($ip) {
            $query->where('ip', 'like', "%{$ip}%");
        }

        $data = $query->paginate($number);

        $data->getCollection()->transform(function (AdminOperationLog $admin_operation_log) {
            $role_names = $admin_operation_log->modelHasRole->filter(function ($modelHasRole) {
                return $modelHasRole->role && $modelHasRole->role->is_show === Role::SHOW;
            })->map(function ($modelHasRole) {
                return $modelHasRole->role->display_name ?? '';
            })->implode(',');

            return [
                'id' => $admin_operation_log->id,
                'admin_user_name' => $admin_operation_log->adminUser->user_name ?? '',
                'role_name' => $role_names,
                'description' => $admin_operation_log->description,
                'ip' => $admin_operation_log->ip,
                'created_at' => $admin_operation_log->created_at,
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
