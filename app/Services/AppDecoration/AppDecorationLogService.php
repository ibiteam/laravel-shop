<?php

namespace App\Services\AppDecoration;

use App\Http\Resources\CommonResourceCollection;
use App\Models\AppDecorationLog;

class AppDecorationLogService
{
    // 记录装修日志
    public function saveLog(?AppDecorationLog $app_decoration_log, int $app_decoration_id, array $app_decoration_item_ids, int $admin_user_id)
    {
        if (! $app_decoration_log) {
            $app_decoration_log = new AppDecorationLog();
        }

        $app_decoration_log->app_decoration_id = $app_decoration_id;
        $app_decoration_log->app_decoration_item_ids = json_encode($app_decoration_item_ids, JSON_UNESCAPED_UNICODE);
        $app_decoration_log->admin_user_id = $admin_user_id;
        $app_decoration_log->save();
    }

    // 获取最新一条记录
    public function getLatestLog(int $app_decoration_id)
    {
        return AppDecorationLog::whereAppDecorationId($app_decoration_id)->orderByDesc('updated_at')->first();
    }

    // 获取所有记录 - 带分页
    public function getAllLogsPaginate(int $app_decoration_id, int $page_size = 10): CommonResourceCollection
    {
        $data = AppDecorationLog::whereAppDecorationId($app_decoration_id)
            ->with('admin_user:id,user_name')
            ->orderByDesc('id')->paginate($page_size);

        $data->getCollection()->transform(function (AppDecorationLog $item) {
            return [
                'log_id' => $item->id,
                'admin_user_name' => $item->admin_user?->user_name,
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        });

        return new CommonResourceCollection($data);
    }

}
