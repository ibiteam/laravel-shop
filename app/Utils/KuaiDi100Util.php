<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class KuaiDi100Util
{
    public static function getLabelByStatus(int $status_code): string
    {
        if (in_array($status_code, [1, 101, 102, 103])) {
            return '已揽件';
        }

        if (in_array($status_code, [0, 1001, 1002, 1003])) {
            return '运输中';
        }

        if (in_array($status_code, [5, 501])) {
            return '派送中';
        }

        if (in_array($status_code, [3, 301, 302, 303, 304])) {
            return '已签收';
        }

        if (in_array($status_code, [6])) {
            return '退回';
        }

        if (in_array($status_code, [4, 401, 14])) {
            return '退签';
        }

        if (in_array($status_code, [2, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 7, 8, 10, 11, 12, 13])) {
            return '疑难';
        }

        return '';
    }

    /**
     * 快递查询.
     *
     * @param string $ship_no           快递单号
     * @param string $ship_company_code 快递公司编码
     * @param string $phone             手机号 选填；（必填：顺丰速运、顺丰快运）
     *
     * @throws \Exception
     */
    public static function queryExpress(string $ship_no, string $ship_company_code, string $phone = ''): array
    {
        $customer = config('custom.kuaidi100.customer');
        $key = config('custom.kuaidi100.key');

        if (! $customer || ! $key) {
            return [];
        }
        $param = json_encode([
            'com' => $ship_company_code, // 快递公司code
            'num' => $ship_no, // 快递单号
            'phone' => $phone, // 手机号
            'from' => '', // 出发地城市
            'to' => '', // 目的地城市
            'resultv2' => 4, // 开启行政区域解析
        ]);

        $data = [
            'customer' => $customer,
            'param' => $param,
            'sign' => strtoupper(md5($param.$key.$customer)),
        ];

        $response = self::doPost('/poll/query.do', $data);

        if (! isset($response['status']) || $response['status'] != Response::HTTP_OK) {
            throw new \Exception('物流信息未查询到！');
        }

        return $response['data'] ?? [];
    }

    private static function doPost(string $url, array $params): array
    {
        $host = config('custom.kuaidi100.host');

        if (! $host) {
            return [];
        }

        try {
            $client = new Client([
                'base_uri' => $host,
                'timeout' => 10,
            ]);
            $response = $client->post($url, [
                'form_params' => $params,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \Exception('请求失败');
            }
            $content = $response->getBody()->getContents();

            if (! $content) {
                throw new \Exception('请求失败:获取 Content 为空');
            }

            return json_decode(html_entity_decode($content), true);
        } catch (\Throwable $throwable) {
            dd($throwable);

            return [];
        }
    }
}
