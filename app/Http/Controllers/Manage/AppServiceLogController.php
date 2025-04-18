<?php

namespace App\Http\Controllers\Manage;

use App\Models\AppServiceConfig;
use App\Models\AppServiceLog;
use Illuminate\Http\Request;

class AppServiceLogController extends BaseController
{
    public function index(Request $request)
    {
        $alias = $request->get('alias', '');
        //        $allConfigData = $this->getConfigData();
        //        $name_list = $allConfigData['name_array'];
        //        $alias_list = $allConfigData['alias_array'];

        $number = $request->input('number', 10);
        $query = AppServiceLog::select(['id', 'service_id', 'user_id', 'created_at'])->with(['app_service_config:id,alias,name', 'user:id,nickname'])->orderByDesc('id');
        $query = $this->getWhere($query, $request);
        $data = $query->paginate($number)->toArray();

        if ($data['data']) {
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['name'] = $v['user']['nickname'] ?? '';
            }
        }

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
