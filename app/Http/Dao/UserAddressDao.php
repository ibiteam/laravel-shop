<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class UserAddressDao
{
    /**
     * 获取用户地址列表.
     */
    public function getAddrListsForUser(int $user_id, $keywords = null): EloquentCollection|Collection
    {
        $query = UserAddress::query()
            ->with(['regionProvince:id,name', 'regionCity:id,name', 'regionDistrict:id,name'])
            ->whereUserId($user_id)
            ->orderByDesc('is_default')
            ->orderByDesc('id');

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

        return $query->limit(60)->get()->map(function (UserAddress $item) {
            return $this->userAddressFormat($item);
        });
    }

    /**
     * 获取地址详情.
     *
     * @throws BusinessException
     */
    public function show(int $user_id, int $id): array
    {
        $item = UserAddress::query()
            ->with(['regionProvince:id,name', 'regionCity:id,name', 'regionDistrict:id,name'])
            ->whereUserId($user_id)
            ->find($id);

        if (! $item) {
            throw new BusinessException('地址不存在，请检查参数');
        }

        return $this->userAddressFormat($item);
    }

    public function resetDefault(int $user_id): void
    {
        UserAddress::whereUserId($user_id)->whereIsDefault(UserAddress::DEFAULT)->update(['is_default' => UserAddress::NOT_DEFAULT]);
    }

    public function replaceAddressByRegionId($provinceId, $cityId, $districtId, $address): string
    {
        [$province,$city,$district] = app(RegionDao::class)->getRegionName([$provinceId, $cityId, $districtId])->pluck('region_name');

        return $this->replaceAddress($province, $city, $district, $address);
    }

    public function replaceAddress($province, $city, $district, $address): string
    {
        if (strpos($address, $province) || strpos($address, $city) || strpos($address, $district)) {
            return '';
        }

        return $address;
    }

    /**
     * 格式化地址数据.
     */
    private function userAddressFormat(UserAddress $user_address): array
    {
        return [
            'id' => $user_address->id,
            'recipient_name' => $user_address->recipient_name,
            'recipient_phone' => $user_address->recipient_phone,
            'province' => $user_address->regionProvince?->name,
            'city' => $user_address->regionCity?->name,
            'district' => $user_address->regionDistrict?->name,
            'address_detail' => $user_address->address_detail,
            'is_default' => $user_address->is_default,
        ];
    }
}
