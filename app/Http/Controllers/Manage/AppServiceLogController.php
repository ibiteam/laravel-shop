<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\AppServiceConfig;
use App\Models\AppServiceLog;
use Illuminate\Http\Request;

class AppServiceLogController extends BaseController
{
    public function index(Request $request)
    {
        $number = $request->input('number', 10);
        $query = AppServiceLog::query();
        $query = $this->getWhere($query, $request);
        $list = $query->select(['id', 'service_id', 'user_id', 'created_at', 'request_param', 'result_data'])
            ->with(['app_service_config:id,alias,name', 'user:id,user_name'])
            ->orderByDesc('id')->paginate($number);
        $list->getCollection()->transform(function (AppServiceLog $app_service_log) {
            $app_service_log->user_name = "【{$app_service_log->user_id}】{$app_service_log->user?->user_name}";
            $app_service_log->name = "{$app_service_log->app_service_config?->name}【{$app_service_log->app_service_config?->alias}】";
            $app_service_log->request_param = json_decode($app_service_log->request_param, true);
            $app_service_log->result_data = json_decode($app_service_log->result_data, true);

            return $app_service_log;
        });
        $data = (new CommonResourceCollection($list))->toArray($request);
        $allConfigData = $this->getConfigData();
        $data['name_list'] = $allConfigData['name_array'];
        $data['alias_list'] = $allConfigData['alias_array'];

        return $this->success($data);
    }

    /**
     * 单条.
     */
    public function info(Request $request)
    {
        $id = $request->get('id');
        $service_log = AppServiceLog::query()->whereId($id)->first();

        if (! $service_log) {
            return $this->error('没有日志信息');
        }

        $service_log_array = $service_log->toArray();

        $data['request_param'] = json_decode($service_log_array['request_param'], true);
        $data['result_data'] = json_decode($service_log_array['result_data'], true);

        return $this->success($data);
    }

    private function getWhere($query, $request)
    {
        $name = $request->get('name', '');
        $alias = $request->get('alias', '');
        $user_name = $request->get('user_name', '');

        if ($name) {
            $query->whereHas('app_service_config', function ($query) use ($name) {
                $query->where('name', $name);
            });
        }

        if ($alias) {
            $query->whereHas('app_service_config', function ($query) use ($alias) {
                $query->where('alias', $alias);
            });
        }

        if ($user_name) {
            $query->whereHas('user', function ($query) use ($user_name) {
                $query->where('nickname', 'like', '%'.$user_name.'%');
            });
        }

        return $query;
    }

    private function getConfigData()
    {
        $all_data = AppServiceConfig::query()->get()->toArray();
        $name_array = array_column($all_data, 'name');
        $alias_array = array_column($all_data, 'alias');

        return [
            'name_array' => $name_array,
            'alias_array' => $alias_array,
        ];
    }
}
