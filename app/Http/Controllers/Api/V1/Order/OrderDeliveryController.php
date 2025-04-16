<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\OrderDeliveryDao;
use App\Http\Dao\UserAddressDao;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Services\ExpressService;
use App\Utils\AppServiceConfig\GeoUtil;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderDeliveryController extends BaseController
{
    /**
     * 发货列表.
     */
    public function list(Request $request, OrderDeliveryDao $order_delivery_dao)
    {
        try {
            $validated = $request->validate([
                'order_no' => 'required|string',
                'page' => 'required|integer|min:1',
                'number' => 'required|integer|min:1',
            ], [], [
                'keywords' => '订单编号',
                'page' => '页码',
                'number' => '每页数量',
            ]);

            $list = $order_delivery_dao->getListByOrder($validated['order_no'], $validated['page'], $validated['number']);

            return $this->success(new CommonResourceCollection($list));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取发货列表异常~');
        }
    }

    /**
     * 物流信息.
     */
    public function logistics(Request $request, ExpressService $express_service, UserAddressDao $user_address_dao)
    {
        try {
            $validated = $request->validate([
                'delivery_no' => 'required|string',
            ], [], [
                'delivery_no' => '发货单编号',
            ]);

            $order_delivery = OrderDelivery::query()->with(['shipCompany', 'order'])
                ->whereDeliveryNo($validated['delivery_no'])
                ->first();

            if (! $order_delivery instanceof OrderDelivery) {
                throw new BusinessException('订单发货单不存在');
            }

            $order = $order_delivery->order;

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            $area_lng_list = [];    // 途径的所有经纬度
            $start_lng_lat = '';    // 起点的经纬度
            $end_lng_lat = '';      // 终点的经纬度
            $current_lng_lat = '';  // 当前地址的经纬度（途径的最新一条）
            $start_area = '';       // 起点的市地区
            $end_area = '';         // 终点的市地区
            $current_area = '';     // 当前地址的地区（途径的最新一条）
            $current_status = '';   // 当前地址的运输状态（途径的最新一条）

            // 快递信息
            $ship_list = $express_service->queryExpress($order_delivery->ship_no, $order_delivery->shipCompany->code ?? '', $order_delivery->order->phone ?? '');

            if ($ship_list) {
                $ship_list = collect($ship_list)->map(function ($item, $key) use (&$area_lng_list) {
                    if ($item['area_center']) {
                        $area_lng_list[$key] = $item['area_center'];
                    }

                    return $item;
                })->toArray();

                // 去重的途径经纬度 起点-终点
                $area_lng_list = array_reverse(array_unique($area_lng_list));

                if (! empty($area_lng_list)) {
                    // 获取起点 市名
                    $start_lng_lat = $area_lng_list[0];
                    $startRegeoData = (new GeoUtil($this->user()->id))->getRegeoByLocation($start_lng_lat);
                    $city = $startRegeoData['regeocode']['addressComponent']['city'] ?? '';
                    $province = $startRegeoData['regeocode']['addressComponent']['province'] ?? '';
                    $start_area = $city ?: $province;

                    // 签收后不展示当前地址
                    if (($ship_list[0]['statusCode'] ?? '') != '已签收') {
                        // 当前节点经纬度
                        $current_lng_lat = end($area_lng_list);
                        // 当前运输状态
                        $current_status = $ship_list[0]['status'] ?? '';
                        // 当前节点市名
                        $endRegeoData = (new GeoUtil($this->user()->id))->getRegeoByLocation($current_lng_lat);
                        $city = $endRegeoData['regeocode']['addressComponent']['city'] ?? '';
                        $province = $endRegeoData['regeocode']['addressComponent']['province'] ?? '';
                        $current_area = $city ?: ($province ?: '未知');
                    }
                }
            }

            // 收货地址
            $address = $user_address_dao->replaceAddressByRegionId($order->province_id, $order->city_id, $order->district_id, $order->address);

            if ($address) {
                $geocoding = (new GeoUtil($this->user()->id))->getGeocodingByAddress($address);
                $end_area = $geocoding['geocodes'][0]['city'] ?? '';
                $end_lng_lat = $geocoding['geocodes'][0]['location'] ?? '';
            }

            $data = [
                'ship_no' => $order_delivery->ship_no,
                'ship_company_name' => $order_delivery->shipCompany?->name,
                'ship_list' => $ship_list,
                'address' => $address,
                'area_lng_list' => $area_lng_list,
                'start_lng_lat' => $start_lng_lat,
                'end_lng_lat' => $end_lng_lat,
                'current_lng_lat' => $current_lng_lat,
                'start_area' => $start_area,
                'end_area' => $end_area,
                'current_area' => $current_area,
                'current_status' => $current_status,
            ];

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取物流详情异常~'.$throwable->getMessage());
        }
    }
}
