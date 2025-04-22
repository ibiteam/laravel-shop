<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Enums\OrderTypeEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\ProcessDataException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CartDao;
use App\Http\Dao\UserAddressDao;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\Order\GoodsFormatter;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DoneController extends BaseController
{
    /**
     * 直接下单回显接口.
     */
    public function directInit(Request $request, OrderService $order_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
                'sku_id' => 'nullable|integer|min:1',
                'buy_number' => 'required|integer',
                'user_address_id' => 'nullable|integer',
            ], [], [
                'no' => '商品编号',
                'buy_number' => '购买数量',
                'user_address_id' => '用户地址ID',
            ]);

            $current_user = get_user();

            $current_goods_format = (new GoodsFormatter)
                ->getFormatter()
                ->setUser($current_user)
                ->setGoodsNo($validated['no'])
                ->setSkuId($validated['sku_id'] ?? 0)
                ->setBuyNumber($validated['buy_number'])
                ->validate();

            $data = $order_service
                ->setUser($current_user)
                ->setIp(get_request_ip())
                ->setGoodsFormatters([$current_goods_format])
                ->calculatePrice()
                ->checkIntegral()
                ->setShippingFee(0)
                ->getInitData($validated['user_address_id'] ?? 0);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (ProcessDataException $process_data_exception) {
            return $this->error($process_data_exception->getMessage(), $process_data_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            dd($throwable);
            return $this->error('操作失败');
        }
    }

    /**
     * 直接下单接口.
     */
    public function directDone(Request $request, UserAddressDao $user_address_dao, OrderService $order_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
                'payment_method' => 'required|string|in:'.implode(',', [Order::PAYMENT_METHOD_ONLINE]),
                'sku_id' => 'nullable|integer|min:1',
                'buy_number' => 'required|integer',
                'user_address_id' => 'required|integer',
                'remark' => 'nullable|string',
            ], [], [
                'no' => '商品编号',
                'payment_method' => '支付方式',
                'buy_number' => '购买数量',
                'user_address_id' => '用户地址ID',
                'remark' => '备注',
            ]);

            $current_user = get_user();

            $user_address = $user_address_dao->getUserAddressById($current_user->id, $validated['user_address_id']);

            if (! $user_address instanceof UserAddress) {
                throw new BusinessException('收货地址不存在');
            }

            $current_goods_format = (new GoodsFormatter)->getFormatter()->setUser($current_user)->setGoodsNo($validated['no'])->setSkuId($validated['sku_id'] ?? 0)->setBuyNumber($validated['buy_number'])->validate();

            $data = $order_service
                ->setUser($current_user)
                ->setOrderTypeEnum(OrderTypeEnum::NORMAL)
                ->setSource(get_source())
                ->setIp(get_request_ip())
                ->setUserAddress($user_address)
                ->setGoodsFormatters([$current_goods_format])
                ->setPaymentMethod($validated['payment_method'])
                ->calculatePrice()
                ->checkIntegral()
                ->setShippingFee(0)
                ->store($validated['remark'] ?? '');

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (ProcessDataException $process_data_exception) {
            return $this->error($process_data_exception->getMessage(), $process_data_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 购物车下单回显接口.
     */
    public function cartInit(Request $request, CartDao $cart_dao, OrderService $order_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_address_id' => 'nullable|integer',
            ], [], [
                'user_address_id' => '用户地址ID',
            ]);

            $current_user = get_user();

            $goods_formatters = $cart_dao->getDoneCartGoods($current_user->id)->map(function (Cart $cart) use ($current_user) {
                return (new GoodsFormatter)
                    ->getFormatter()
                    ->setUser($current_user)
                    ->setCartId($cart->id)
                    ->setGoods($cart->goods)
                    ->setSkuId($cart->goods_sku_id)
                    ->setBuyNumber($cart->buy_number)
                    ->validate();
            });

            if ($goods_formatters->isEmpty()) {
                throw new BusinessException('请先选择购物车中要购买的商品');
            }

            $data = $order_service
                ->setUser($current_user)
                ->setIp(get_request_ip())
                ->setGoodsFormatters($goods_formatters->toArray())
                ->calculatePrice()
                ->checkIntegral()
                ->setShippingFee(0)
                ->getInitData($validated['user_address_id'] ?? 0);

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (ProcessDataException $process_data_exception) {
            return $this->error($process_data_exception->getMessage(), $process_data_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 购物车下单接口.
     */
    public function cartDone(Request $request, CartDao $cart_dao, UserAddressDao $user_address_dao, OrderService $order_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_address_id' => 'required|integer',
                'payment_method' => 'required|string|in:'.implode(',', [Order::PAYMENT_METHOD_ONLINE]),
                'remark' => 'nullable|string',
            ], [], [
                'user_address_id' => '用户地址ID',
                'payment_method' => '支付方式',
                'remark' => '备注',
            ]);

            $current_user = get_user();

            $user_address = $user_address_dao->getUserAddressById($current_user->id, $validated['user_address_id']);

            if (! $user_address instanceof UserAddress) {
                throw new BusinessException('收货地址不存在');
            }

            $goods_formatters = $cart_dao->getDoneCartGoods($current_user->id)->map(function (Cart $cart) use ($current_user) {
                return (new GoodsFormatter)
                    ->getFormatter()
                    ->setUser($current_user)
                    ->setCartId($cart->id)
                    ->setGoods($cart->goods)
                    ->setSkuId($cart->goods_sku_id)
                    ->setBuyNumber($cart->buy_number)
                    ->validate();
            });

            if ($goods_formatters->isEmpty()) {
                throw new BusinessException('请先选择购物车中要购买的商品');
            }

            $data = $order_service
                ->setUser($current_user)
                ->setOrderTypeEnum(OrderTypeEnum::NORMAL)
                ->setSource(get_source())
                ->setIp(get_request_ip())
                ->setUserAddress($user_address)
                ->setGoodsFormatters($goods_formatters->toArray())
                ->setPaymentMethod($validated['payment_method'])
                ->calculatePrice()
                ->checkIntegral()
                ->setShippingFee(0)
                ->store($validated['remark'] ?? '');

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (ProcessDataException $process_data_exception) {
            return $this->error($process_data_exception->getMessage(), $process_data_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
