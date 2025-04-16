<?php

declare(strict_types=1);

namespace App\Utils\AppServiceConfig;

use App\Models\AppServiceConfig;

class GeoUtil extends AppServiceBase
{
    private array $settings;

    public function __construct(int $user_id)
    {
        parent::__construct($user_id);
        $this->settings = (array) $this->getConfig()->config;
    }

    /**
     * 通过IP获取地址信息.
     *
     * @throws \Exception
     */
    public function getAddressByIp(string $ip)
    {
        $query = array_filter([
            'key' => $this->settings['key'],
            'ip' => $ip,
            'output' => 'json',
        ]);

        try {
            $data = [
                'query' => $query,
            ];

            return $this->doGet('v3/ip', $data);
        } catch (\Exception $exception) {
            throw new \Exception('通过IP获取地址信息异常~'.$exception->getMessage());
        }
    }

    /**
     * 逆地理编码
     *
     * @throws \Exception
     */
    public function getRegeoByLocation($location, string $poitype = '', int $radius = 100, string $type = 'all', bool $batch = false, int $roadlevel = 0)
    {
        $query = array_filter([
            'key' => $this->settings['key'],
            'location' => $location,
            'poitype' => $poitype,
            'radius' => $radius,
            'extensions' => $type,
            'batch' => $batch,
            'roadlevel' => $roadlevel,
            'output' => 'json',
        ]);

        try {
            $data = [
                'query' => $query,
            ];

            return $this->doGet('v3/geocode/regeo', $data);
        } catch (\Exception $exception) {
            throw new \Exception('获取逆地理编码信息异常~'.$exception->getMessage());
        }
    }

    /**
     * 根据地址获取经纬度.
     *
     * @throws \Exception
     */
    public function getGeocodingByAddress($address)
    {
        $query = array_filter([
            'key' => $this->settings['key'],
            'address' => $address,
            'output' => 'json',
        ]);

        try {
            $data = [
                'query' => $query,
            ];

            return $this->doGet('v3/geocode/geo', $data);
        } catch (\Exception $exception) {
            throw new \Exception('根据地址获取经纬度异常~'.$exception->getMessage());
        }
    }

    protected static function getAlias(): string
    {
        return AppServiceConfig::GEO_AMAP;
    }
}
