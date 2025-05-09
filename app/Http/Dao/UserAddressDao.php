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
                    $q->where('phone', 'like', '%'.$keywords.'%');
                });
            } else {
                $query->where(function ($qe) use ($keywords) {
                    $qe->where('consignee', 'like', '%'.$keywords.'%');
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
        $item = $this->getUserAddressById($user_id, $id);

        if (! $item) {
            throw new BusinessException('地址不存在，请检查参数');
        }

        return $this->userAddressFormat($item);
    }

    /**
     * 获取地址详情-收货地址ID或默认地址或第一条地址
     */
    public function showByIdOrDefault(int $user_id, int $id): ?array
    {
        if ($id > 0) {
            // 尝试通过ID获取地址
            $user_address = $this->getUserAddressById($user_id, $id);

            if ($user_address instanceof UserAddress) {
                return $this->userAddressFormat($user_address);
            }
        }

        // 尝试获取默认地址
        $user_address = $this->getUserAddressByDefault($user_id);

        if ($user_address instanceof UserAddress) {
            return $this->userAddressFormat($user_address);
        }

        $user_address = $this->getFirstUserAddress($user_id);

        if ($user_address instanceof UserAddress) {
            return $this->userAddressFormat($user_address);
        }

        return null;
    }

    public function resetDefault(int $user_id): void
    {
        UserAddress::whereUserId($user_id)->whereIsDefault(UserAddress::DEFAULT)->update(['is_default' => UserAddress::NOT_DEFAULT]);
    }

    public function replaceAddressByRegionId(int $provinceId, int $cityId, int $districtId, string $address): string
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
     * 获取当前用户的用户地址
     */
    public function getUserAddressById(int $user_id, int $id): ?UserAddress
    {
        return UserAddress::query()
            ->with(['regionProvince:id,name', 'regionCity:id,name', 'regionDistrict:id,name'])
            ->whereUserId($user_id)
            ->whereId($id)
            ->first();
    }

    /**
     * 获取当前用户默认地址.
     */
    private function getUserAddressByDefault(int $user_id): ?UserAddress
    {
        return UserAddress::query()
            ->with(['regionProvince:id,name', 'regionCity:id,name', 'regionDistrict:id,name'])
            ->whereUserId($user_id)
            ->whereIsDefault(UserAddress::DEFAULT)
            ->first();
    }

    /**
     * 获取当前用户的第一个地址.
     */
    private function getFirstUserAddress(int $user_id): ?UserAddress
    {
        return UserAddress::query()
            ->with(['regionProvince:id,name', 'regionCity:id,name', 'regionDistrict:id,name'])
            ->whereUserId($user_id)
            ->first();
    }

    /**
     * 格式化地址数据.
     */
    private function userAddressFormat(UserAddress $user_address): array
    {
        return [
            'id' => $user_address->id,
            'consignee' => $user_address->consignee,
            'phone' => $user_address->phone,
            'province' => $user_address->regionProvince?->name,
            'city' => $user_address->regionCity?->name,
            'district' => $user_address->regionDistrict?->name,
            'province_id' => $user_address->province,
            'city_id' => $user_address->city,
            'district_id' => $user_address->district,
            'address_detail' => $user_address->address_detail,
            'is_default' => $user_address->is_default,
        ];
    }
}
