<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\RegionDao;
use App\Http\Dao\UserAddressDao;
use App\Http\Requests\BatchDestroyAddressRequest;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    /**
     * 获取地址列表.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = app(UserAddressDao::class)
            ->getAddrListsForUser($request->user()?->id ?: 0)
            ->toArray();

        return $this->success($data);
    }

    /**
     * 搜索收货地址
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search_address(Request $request)
    {
        $data = app(UserAddressDao::class)
            ->getAddrListsForUser($request->user()?->id ?: 0, $request->get('keywords'))
            ->toArray();

        return $this->success($data);
    }

    // 获取地址详情
    public function show(Request $request)
    {
        $user = $request->user();

        try {
            $id = $request->get('id');
            $data = app(UserAddressDao::class)->show($user->id, $id);
        } catch (BusinessException $exception) {
            return $this->error($exception->getMessage());
        }

        return $this->success($data);
    }

    // 删除地址
    public function destroy(Request $request)
    {
        $address = UserAddress::query()
            ->whereUserId($request->user()?->id ?: 0)
            ->whereId($request->get('id'))->first();

        if (! $address) {
            return $this->error('此地址不存在');
        }

        $res = $address->delete();

        if (! $res) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }

    /**
     * 批量删除收货地址
     */
    public function batch_destroy(BatchDestroyAddressRequest $request)
    {
        $validate = $request->validated();
        $user = $request->user();
        $res = UserAddress::whereUserId($user->id)->whereIn('id', $validate['ids'])->delete();

        if (! $res) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }

    // 设置默认地址
    public function setDefault(Request $request, UserAddressDao $userAddressDao)
    {
        $user_id = $request->user()?->id ?: 0;
        $address = UserAddress::whereUserId($user_id)
            ->whereId($request->get('id'))->first();

        if (! $address) {
            return $this->error('此地址不存在');
        }
        // 重置默认地址
        $userAddressDao->resetDefault($user_id);

        $address->update(['is_default' => UserAddress::DEFAULT]);

        return $this->success('保存成功');
    }

    // 添加
    public function update(UserAddressRequest $request, UserAddressDao $userAddressDao)
    {
        $validated = $request->validated();
        $user_id = $request->user()?->id ?: 0;
        $id = $validated['id'] ?? 0;

        if ($id) {
            $address = UserAddress::query()->whereUserId($user_id)->whereId($id)->first();

            if (! $address) {
                return $this->error('此地址数据不存在');
            }
        } else {
            $address = new UserAddress;
        }
        $address->user_id = $user_id;
        $address->recipient_name = $validated['recipient_name'];
        $address->recipient_phone = $validated['recipient_phone'];
        $address->province = $validated['province'];
        $address->city = $validated['city'];
        $address->district = $validated['district'];
        $address_detail = $validated['address_detail'];
        $is_default = $validated['is_default'];
        $detail_address = app(UserAddressDao::class)
            ->replaceAddressByRegionId($address->province, $address->city, $address->district, $address_detail);

        if (! $detail_address) {
            return $this->error('详细地址中请勿重复填写省市区信息，请修改！');
        }
        $address->address_detail = $address_detail;

        if ($is_default) {
            $userAddressDao->resetDefault($user_id);
        }
        $address->is_default = $is_default;
        $address->save();

        if ($address) {
            return $this->success('保存成功');
        }

        return $this->error('保存失败');
    }

    // 获取地区数据
    public function region()
    {
        return $this->success(app(RegionDao::class)->getRegionTree());
    }

    /**
     * 获取地区数据-地区分组.
     *
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @throws \App\Exceptions\BusinessException
     */
    public function regionGroup()
    {
        $result = app(RegionDao::class)->getRegionTree();

        foreach ($result as &$child_result) {
            if ($child_result['children'] ?? []) {
                $child_result['children'] = $child_result['children']->toArray();
                usort($child_result['children'], function ($a, $b) {
                    return $a['pinyin_first'] <=> $b['pinyin_first'];
                });

                foreach ($child_result['children'] as &$child) {
                    if ($child['children'] ?? []) {
                        $child['children'] = $child['children']->toArray();
                        usort($child['children'], function ($a, $b) {
                            return $a['pinyin_first'] <=> $b['pinyin_first'];
                        });
                        $second_child = collect($child['children'])->groupBy('pinyin_first')->toArray();

                        if (is_app_request()) {
                            unset($child['children']);

                            foreach ($second_child as $key => $temp_children) {
                                $child['list'][$key]['key'] = $key;
                                $child['list'][$key]['children'] = $temp_children;
                            }
                            $child['list'] = isset($child['list']) ? array_values($child['list']) : [];
                        } else {
                            $child['children'] = $second_child;
                        }
                    }
                }
                $third_child = collect($child_result['children'])->groupBy('pinyin_first')->toArray();

                if (is_app_request()) {
                    unset($child_result['children']);

                    foreach ($third_child as $key => $temp_children) {
                        $child_result['list'][$key]['key'] = $key;
                        $child_result['list'][$key]['children'] = $temp_children;
                    }
                    $child_result['list'] = isset($child_result['list']) ? array_values($child_result['list']) : [];
                } else {
                    $child_result['children'] = $third_child;
                }
            }
        }
        usort($result, function ($a, $b) {
            return $a['pinyin_first'] <=> $b['pinyin_first'];
        });
        $result = collect($result)->groupBy('pinyin_first')->toArray();

        if (is_app_request()) {
            $res = [];

            foreach ($result as $key => $temp_children) {
                $res[$key]['key'] = $key;
                $res[$key]['children'] = $temp_children;
            }
            $result = array_values($res);
        }

        return $this->success($result);
    }
}
