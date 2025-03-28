<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CartDao;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsSku;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 购物车.
 */
class CartController extends BaseController
{
    /**
     * 购物车商品列表.
     */
    public function list(Request $request, CartDao $cart_dao)
    {
        $user = $this->user();

        try {
            $data = $cart_dao->cartGoodsList($user->id);

            return $this->success($data);
        } catch (\Throwable $throwable) {
            return $this->error('获取购物车商品列表异常~'.$throwable->getMessage());
        }
    }

    /**
     * 获取购物车数量.
     */
    public function number(Request $request, CartDao $cart_dao)
    {
        $user = $this->user();

        try {
            $data['number'] = $cart_dao->getValidCarNumber($user->id);

            return $this->success($data);
        } catch (\Throwable $throwable) {
            return $this->error('获取购物车数量异常~');
        }
    }

    /**
     * 添加购物车.
     */
    public function store(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'goods_id' => 'required|int',
                'goods_sku_id' => 'required|int',
                'buy_number' => 'required|int',
            ], [], [
                'goods_id' => '商品ID',
                'goods_sku_id' => '商品规格ID',
                'buy_number' => '购买数量',
            ]);

            $goods_id = $validated['goods_id'];
            $goods_sku_id = $validated['goods_sku_id'];
            $buy_number = ($validated['buy_number'] <= 0) ? 1 : $validated['buy_number'];

            $goods = Goods::query()->withCount('skus')->whereId($goods_id)->show()->first();

            if (! $goods) {
                throw new BusinessException('该商品已不能购买');
            }

            if ($goods_sku_id) {
                if (! $goods->skus_count) {
                    throw new BusinessException('商品规格发生变更');
                }

                if (! GoodsSku::whereId($goods_sku_id)->whereGoodsId($goods_id)->first()) {
                    throw new BusinessException('商品规格错误');
                }
            } else {
                if ($goods->skus_count) {
                    throw new BusinessException('请选择商品规格');
                }
            }

            $cart = Cart::query()->whereUserId($user->id)->whereGoodsId($goods_id)->whereGoodsSkuId($goods_sku_id)->first();

            if ($cart) {
                $cart->buy_number = $buy_number + $cart->buy_number;
            } else {
                // 添加
                $cart = new Cart;
                $cart->user_id = $user->id;
                $cart->goods_id = $goods_id;
                $cart->goods_sku_id = $goods_sku_id;
                $cart->buy_number = $buy_number;
            }

            if (! $cart->save()) {
                throw new BusinessException('添加购物车失败');
            }

            return $this->success('添加成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('购物车添加异常~');
        }
    }

    /**
     * 删除 (一个/多个).
     */
    public function destroy(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'ids' => 'required|array',
            ], [], [
                'ids' => '购物车ID',
            ]);

            $ids = $validated['ids'];

            $flag = Cart::whereUserId($user->id)->whereIn('id', $ids)->delete();

            if (! $flag) {
                throw new BusinessException('删除失败');
            }

            return $this->success('删除成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('购物车删除异常~');
        }
    }

    /**
     * 变更商品数量.
     */
    public function changeNumber(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'id' => 'required|int',
                'goods_id' => 'required|int',
                'goods_sku_id' => 'required|int',
                'buy_number' => 'required|int',
            ], [], [
                'id' => '购物车ID',
                'goods_id' => '商品ID',
                'goods_sku_id' => '商品规格ID',
                'buy_number' => '购买数量',
            ]);

            $id = $validated['id'];
            $goods_id = $validated['goods_id'];
            $goods_sku_id = $validated['goods_sku_id'];
            $buy_number = ($validated['buy_number'] <= 0) ? 1 : $validated['buy_number'];

            $cart = Cart::query()->whereId($id)->whereUserId($user->id)->whereGoodsId($goods_id)
                ->when($goods_sku_id > 0, fn (Builder $query) => $query->whereGoodsSkuId($goods_sku_id))
                ->first();

            if (! $cart) {
                throw new BusinessException('购物车商品不存在');
            }

            $goods = Goods::query()->whereId($goods_id)->show()->first();

            if (! $goods) {
                throw new BusinessException('商品无效');
            }

            if ($goods->total <= 0 || $goods->total < $buy_number) {
                throw new BusinessException('商品库存不足');
            }

            $cart->buy_number = $buy_number;
            $cart->is_check = Cart::IS_CHECK_YES;

            if (! $cart->save()) {
                throw new BusinessException('变更商品数量失败');
            }

            return $this->success('变更商品数量成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('变更商品数量异常~');
        }
    }

    /**
     * 变更选中结算 (单个/全部).
     */
    public function changeCheck(Request $request)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'goods_id' => 'required|int',
                'goods_sku_id' => 'required|int',
                'is_check' => 'required|int|in:0,1',
            ], [], [
                'goods_id' => '商品ID',
                'goods_sku_id' => '商品规格ID',
                'is_check' => '是否选中结算',
            ]);

            $goods_id = $validated['goods_id'];
            $goods_sku_id = $validated['goods_sku_id'];
            $is_check = $validated['is_check'];

            if ($goods_id == 0) {
                // goods_id=0 代表：全选/全不选
                $flag = Cart::query()->whereUserId($user->id)->update(['is_check' => $is_check]);

                if (! $flag) {
                    throw new BusinessException('变更选中失败');
                }
            } else {
                // goods_id>0 代表：单个商品
                $cart = Cart::query()->whereUserId($user->id)->whereGoodsId($goods_id)
                    ->when($goods_sku_id > 0, fn (Builder $query) => $query->whereGoodsSkuId($goods_sku_id))
                    ->first();

                if (! $cart) {
                    throw new BusinessException('购物车商品不存在');
                }

                $cart->is_check = $is_check;

                if (! $cart->save()) {
                    throw new BusinessException('变更是否选中失败');
                }
            }

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('变更是否选中异常~');
        }
    }

}
