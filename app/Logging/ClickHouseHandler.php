<?php

namespace App\Logging;

use App\Models\Clickhouse\ErrorLog;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class ClickHouseHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        // 保存到 clickhouse
        try {
            // 是否格式化日志信息
            $formatted = $record->formatted;

            if (! is_array($formatted)) {
                $data = [
                    'channel' => $record->channel,
                    'level' => $record->level->getName(),
                    'message' => $record->message,
                    'context' => json_encode($record->context, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES),
                    'datetime' => $record->datetime->format('Y-m-d H:i:s'),
                    'extra' => json_encode($record->extra, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES),
                ];
            } else {
                $data = [
                    'channel' => $formatted['channel'],
                    'level' => $formatted['level'],
                    'message' => $formatted['message'],
                    'context' => json_encode($formatted['context'], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES),
                    'datetime' => $formatted['datetime'],
                    'extra' => json_encode($formatted['extra'], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES),
                ];
            }
            ErrorLog::create($data);
        } catch (\Exception $e) {
            Log::channel('daily')->error('保存错误日志到 clickhouse 失败,数据信息：', $data ?? []);
            Log::channel('daily')->error('保存错误日志到 clickhouse 失败'.$e->getMessage(), $e->getTrace());
        }
    }
}
