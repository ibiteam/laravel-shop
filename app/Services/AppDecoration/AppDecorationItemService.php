<?php

namespace App\Services\AppDecoration;

use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;

class AppDecorationItemService
{
    /**
     * 独立校验 time 字段.
     */
    public function validateTimeFields(string $component_name, array $time, string|int $id, int $index = 0)
    {
        // 检查 time 是否为数组且长度为 2
        if (! is_array($time) || count($time) !== 2) {
            throw new ProcessDataException("{$component_name} ：第 {$index} 条数据的时间字段必须是一个包含两个时间的数组", ['id' => $id]);
        }

        $start_time = $time[0] ?? null;
        $end_time = $time[1] ?? null;

        if (! $start_time || ! $end_time) {
            throw new ProcessDataException("{$component_name} ：第 {$index} 条数据的时间字段不能为空", ['id' => $id]);
        }

        if (! strtotime($start_time) || ! strtotime($end_time)) {
            throw new ProcessDataException("{$component_name} ：第 {$index} 条数据的时间字段必须是有效的日期时间格式", ['id' => $id]);
        }

        // 检查开始时间是否早于或等于结束时间
        if (strtotime($start_time) > strtotime($end_time)) {
            throw new ProcessDataException("{$component_name} ：第 {$index} 条数据的开始时间不能晚于结束时间", ['id' => $id]);
        }
    }
}
