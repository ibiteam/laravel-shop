<?php

namespace App\Http\Dao;

use App\Enums\RefererEnum;
use App\Models\AccessStatistic;
use App\Services\AccessLog\AccessLogService;
use App\Services\AccessLog\Factories\AccessLogInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AccessStatisticDao
{
    public function statistic(): void
    {
        $datetime = Carbon::yesterday();
        $statistic_date = $datetime->toDateString();

        $referer_map = [
            RefererEnum::H5->value,
            RefererEnum::PC->value,
            RefererEnum::APP->value,
            RefererEnum::WECHAT_MINI->value
        ];

        $log_data = [];

        try {
            // 读取日志
            $log_data = $this->readLogsByDate($datetime);
        } catch (\Exception $e) {
            Log::error('访问统计，读取日志失败：'.$e->getMessage());
        }

        foreach ($referer_map as $referer) {
            $temp_statistic = AccessStatistic::query()->whereStatisticDate($statistic_date)->whereReferer($referer)->first();

            if ($temp_statistic instanceof AccessStatistic) {
                continue;
            }
            $temp_statistic = new AccessStatistic;
            $temp_statistic->statistic_date = $statistic_date;
            $temp_statistic->referer = $referer;
            $temp_statistic->pv_number = $log_data[$referer]['pv_number'] ?? 0;
            $temp_statistic->uv_number = $log_data[$referer]['uv_number'] ?? 0;
            $temp_statistic->ip_number = $log_data[$referer]['ip_number'] ?? 0;
            $temp_statistic->save();
        }

        Log::info('访问统计执行完毕：'.$statistic_date);
    }

    private function readLogsByDate(DateTimeInterface $datetime): array
    {
        $start_datetime = $datetime->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $end_datetime = $datetime->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $access_log_service = AccessLogService::init();

        if (! $access_log_service instanceof AccessLogInterface) {
            Log::error('访问统计，驱动配置错误');

            return [];
        }

        $access_log_driver = config('custom.access_log_driver');

        if ($access_log_driver == 'mysql') {
            $sql = "SELECT COUNT(*) AS pv_number, COUNT(DISTINCT user_id) AS uv_number, COUNT(DISTINCT ip) AS ip_number, source FROM access_logs WHERE access_datetime >= '{$start_datetime}' AND access_datetime <= '{$end_datetime}' GROUP BY source";

            try {
                $data = $access_log_service->read($sql);
                $data = collect($data)->mapWithKeys(function ($item) {
                    return [$item->source => [
                        'pv_number' => $item->pv_number,
                        'uv_number' => $item->uv_number,
                        'ip_number' => $item->ip_number,
                    ]];
                });

                return $data->toArray();
            } catch (\Exception $e) {
                Log::error('mysql访问统计，执行失败：'.$e->getMessage());

                return [];
            }
        } elseif ($access_log_driver == 'clickhouse') {
            $sql = "SELECT COUNT(*) AS pv_number, COUNT(DISTINCT user_id) AS uv_number, COUNT(DISTINCT ip) AS ip_number, source FROM access_log WHERE access_datetime >= '{$start_datetime}' AND access_datetime <= '{$end_datetime}' GROUP BY source";

            try {
                $data = $access_log_service->read($sql);

                return array_column($data, null, 'source');
            } catch (\Exception $e) {
                Log::error('clickhouse访问统计，执行失败：'.$e->getMessage());

                return [];
            }
        } elseif ($access_log_driver == 'file') {
            try {
                $data = $access_log_service->read(date: $datetime);

                $data = collect($data)->map(function ($item) {
                    return json_decode($item, true);
                });

                $data = $data->groupBy('source')->map(function ($item) {
                    return [
                        'pv_number' => $item->count(),
                        'uv_number' => $item->unique('user_id')->count(),
                        'ip_number' => $item->unique('ip')->count(),
                    ];
                });

                return $data->toArray();
            } catch (\Exception $e) {
                Log::error('file访问统计，执行失败：'.$e->getMessage());

                return [];
            }
        }

        return [];
    }
}
