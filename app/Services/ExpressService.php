<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Utils\KuaiDi100Util;

class ExpressService
{
    /**
     * @throws \Exception
     */
    public function queryExpress(string $ship_no, string $ship_company_code, string $phone = ''): array
    {
        try {
            $response = KuaiDi100Util::queryExpress($ship_no, $ship_company_code, $phone);
            $result = [];

            foreach ($response as $item) {
                $result[] = [
                    'time' => $item['time'] ?? '',
                    'context' => $item['context'] ?? '',
                    'area_name' => $item['areaName'] ?? '',
                    'area_center' => $item['areaCenter'] ?? '',
                    'status' => $item['status'] ?? '',
                    'statusCode' => isset($item['statusCode']) ? KuaiDi100Util::getLabelByStatus($item['statusCode']) : '',
                ];
            }

            return $result;
        } catch (\Throwable $throwable) {
            throw new BusinessException('物流信息未查询到');
        }
    }
}
