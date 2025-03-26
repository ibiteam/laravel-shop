<?php


namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\UserAddress;

class UserAddressDao
{
    /**
     * 获取用户地址列表
     * @param int $user_id
     * @param $keywords
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getAddrListsForUser(int $user_id, $keywords = null)
    {
        $query = UserAddress::query()
            ->select('id','user_id','recipient_name','recipient_phone','province','city','district','address_detail','is_default')
            ->whereUserId($user_id)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->limit(60); // 最多返回 60 个地址

        if ($keywords) {
            if (is_numeric($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    $q->where('recipient_phone', 'like', '%'.$keywords.'%');
                });
            } else {
                $query->where(function ($qe) use ($keywords) {
                    $qe->where('recipient_name', 'like', '%'.$keywords.'%');
                });
            }
        }

        $data = $query->get()->map(function (UserAddress $item) use ($keywords) {
            $addr = app(RegionDao::class)->getRegionName([$item->province, $item->city, $item->district]);
            $item->province_name = $addr[0]['name'] ?? '';
            $item->city_name = $addr[1]['name'] ?? '';
            $item->district_name = $addr[2]['name'] ?? '';
            $item->recipient_phone = phone_hidden($item->recipient_phone);

            return $item;
        });

        return $data;
    }

    /**
     * 获取地址详情
     * @param $user_id
     * @param $id
     * @return array|mixed[]
     * @throws BusinessException
     */
    public function show($user_id, $id)
    {
        $item = UserAddress::query()
            ->select('id','user_id','recipient_name','recipient_phone','province','city','district','address_detail','is_default')
            ->whereUserId($user_id)
            ->find($id);

        if (! $item) {
            throw new BusinessException('地址不存在，请检查参数');
        }
        $addr = app(RegionDao::class)->getRegionName([$item->province, $item->city, $item->district]);
        $item->province_name = $addr[0]['name'] ?? '';
        $item->city_name = $addr[1]['name'] ?? '';
        $item->district_name = $addr[2]['name'] ?? '';
        $item->recipient_phone = phone_hidden($item->recipient_phone);

        return $item->toArray();
    }

    public function resetDefault(int $user_id)
    {
        UserAddress::whereUserId($user_id)->whereIsDefault(UserAddress::DEFAULT)->update(['is_default' => UserAddress::NOT_DEFAULT]);
    }

    public function replaceAddressByRegionId($provinceId, $cityId, $districtId, $address): string
    {
        [$province,$city,$district] = app(RegionDao::class)->getRegionName([$provinceId, $cityId, $districtId])
            ->pluck('region_name');

        return $this->replaceAddress($province, $city, $district, $address);
    }

    public function replaceAddress($province, $city, $district, $address): string
    {
        if (strpos($address, $province) || strpos($address, $city) || strpos($address, $district)) {
            return '';
        }

        return $address;
    }
}
