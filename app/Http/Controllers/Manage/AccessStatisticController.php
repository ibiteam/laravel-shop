<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\IndexRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AccessStatistic;
use Illuminate\Http\Request;

/**
 * 访问统计.
 */
class AccessStatisticController extends BaseController
{
    public function index(IndexRequest $request)
    {
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $referer = $request->get('referer', '');
        $per_page = (int) $request->get('per_page', 10);

        $data = AccessStatistic::query()
            ->when(! is_null($referer), fn ($query) => $query->where('referer', '=', $referer))
            ->when($start_time, fn ($query) => $query->where('statistic_date', '>=', date('Y-m-d', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('statistic_date', '<=', date('Y-m-d', strtotime($end_time))))
            ->orderByDesc('statistic_date')
            ->paginate($per_page);

        $data->getCollection()->transform(function (AccessStatistic $access_statistic) {
            return [
                'statistic_date' => $access_statistic->statistic_date,
                'referer' => $access_statistic->referer,
                'pv_number' => $access_statistic->pv_number,
                'uv_number' => $access_statistic->uv_number,
                'ip_number' => $access_statistic->ip_number,
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    public function chart(Request $request)
    {
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $referer = $request->get('referer', '');

        $data = [];

        $collection = AccessStatistic::query()
            ->when(! is_null($referer), fn ($query) => $query->where('referer', '=', $referer))
            ->when($start_time, fn ($query) => $query->where('statistic_date', '>=', date('Y-m-d', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('statistic_date', '<=', date('Y-m-d', strtotime($end_time))))
            ->orderByDesc('statistic_date')
            ->get();

        if ($collection->isEmpty()) {
            return $this->success($data);
        }

        $chart_data = $collection->sortBy('statistic_date')->groupBy('referer');

        $referer = [
            'pc' => 'PC端',
            'h5' => 'H5端',
            'app' => 'APP端',
            'wechat_mini' => '微信小程序',
        ];

        foreach ($referer as $referer_key => $referer_name) {
            $tmp_item = [
                'name' => $referer_name,
                'statistic_date' => [],
                'pv_number' => [],
                'uv_number' => [],
                'ip_number' => [],
            ];
            $tmp_referer_collect = $chart_data[$referer_key] ?? collect();

            if ($tmp_referer_collect->isEmpty()) {
                $data[$referer_key] = $tmp_item;

                continue;
            }

            $tmp_item = array_merge($tmp_item, [
                'statistic_date' => $tmp_referer_collect->pluck('statistic_date'),
                'pv_number' => $tmp_referer_collect->pluck('pv_number'),
                'uv_number' => $tmp_referer_collect->pluck('uv_number'),
                'ip_number' => $tmp_referer_collect->pluck('ip_number'),
            ]);
            $data[$referer_key] = $tmp_item;
        }

        return $this->success($data);
    }
}
