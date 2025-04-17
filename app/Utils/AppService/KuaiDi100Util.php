<?php

namespace App\Utils\AppService;

use App\Models\AppServiceConfig;
use Symfony\Component\HttpFoundation\Response;

class KuaiDi100Util extends BaseUtil
{
    private array $settings = [
        'host' => '',
        'key' => '',
        'customer' => '',
    ];

    public function __construct(int $user_id)
    {
        parent::__construct($user_id);

        $this->setSettings();
    }

    public function getLabelByStatus(int $status_code): string
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
     * @param string $phone             手机号
     *
     * @throws \Exception
     */
    public function queryExpress(string $ship_no, string $ship_company_code, string $phone = ''): array
    {
        try {
            $param = json_encode([
                'com' => $ship_company_code,
                'num' => $ship_no,
                'phone' => $phone,
                'from' => '', // 出发地城市
                'to' => '', // 目的地城市
                'resultv2' => 4, // 开启行政区域解析
            ]);

            $data = [
                'customer' => $this->settings['customer'],
                'param' => $param,
                'sign' => strtoupper(md5($param.$this->settings['key'].$this->settings['customer'])),
            ];
            $response = $this->doPost('/poll/query.do', ['form_params' => $data]);

            if (! isset($response['status']) || $response['status'] != Response::HTTP_OK) {
                throw new \Exception('物流信息未查询到！');
            }

            return $response['data'] ?? [];
        } catch (\Throwable $throwable) {
            throw new \Exception('物流信息未查询到！');
        }
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(): void
    {
        $this->settings = array_merge($this->settings, $this->config->config);
    }

    protected static function getAlias(): string
    {
        return AppServiceConfig::KUAI_DI_100;
    }
}
