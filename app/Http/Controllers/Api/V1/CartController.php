<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CartDao;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsCollect;
use App\Models\GoodsSku;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * 购物车.
 */
class CartController extends BaseController
{
    /**
     * 购物车商品列表.
     */
    public function list(CartDao $cart_dao)
    {
        try {
            return $this->success($cart_dao->cartGoodsList(get_user()));
        } catch (\Throwable $throwable) {
            return $this->error('购物车异常~');
        }
    }

    /**
     * 获取购物车数量.
     */
    public function number(CartDao $cart_dao)
    {
        $user = get_user();

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
    public function store(Request $request, CartDao $cart_dao)
    {
        $user = get_user();

        try {
            $validated = $request->validate([
                'goods_no' => 'required|string',
                'goods_sku_id' => 'required|int',
                'buy_number' => 'required|int',
            ], [], [
                'goods_no' => '商品编号',
                'goods_sku_id' => '商品规格ID',
                'buy_number' => '购买数量',
            ]);

            $goods_no = $validated['goods_no'];
            $goods_sku_id = $validated['goods_sku_id'];
            $buy_number = ($validated['buy_number'] <= 0) ? 1 : $validated['buy_number'];

            $goods = Goods::query()->withCount('skus')->whereNo($goods_no)->show()->first();

            if (! $goods) {
                throw new BusinessException('该商品已不能购买');
            }

            if ($goods_sku_id) {
                if (! $goods->skus_count) {
                    throw new BusinessException('商品规格发生变更');
                }

                if (! GoodsSku::whereId($goods_sku_id)->whereGoodsId($goods->id)->first()) {
                    throw new BusinessException('商品规格错误');
                }
            } else {
                if ($goods->skus_count) {
                    throw new BusinessException('请选择商品规格');
                }
            }

            $cart = Cart::query()->whereUserId($user->id)->whereGoodsId($goods->id)->whereGoodsSkuId($goods_sku_id)->first();

            if ($cart) {
                $cart->buy_number = $buy_number + $cart->buy_number;
            } else {
                // 添加
                $cart = new Cart;
                $cart->user_id = $user->id;
                $cart->goods_id = $goods->id;
                $cart->goods_sku_id = $goods_sku_id;
                $cart->buy_number = $buy_number;
            }

            if (! $cart->save()) {
                throw new BusinessException('添加购物车失败');
            }

            // 购物车数量
            $data['number'] = $cart_dao->getValidCarNumber($user->id);

            return $this->success($data);
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
        $user = get_user();

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
        $user = get_user();

        try {
            $validated = $request->validate([
                'id' => 'required|int',
                'goods_no' => 'required|string',
                'goods_sku_id' => 'required|int',
                'buy_number' => 'required|int',
            ], [], [
                'id' => '购物车ID',
                'goods_no' => '商品编号',
                'goods_sku_id' => '商品规格ID',
                'buy_number' => '购买数量',
            ]);

            $id = $validated['id'];
            $goods_no = $validated['goods_no'];
            $goods_sku_id = $validated['goods_sku_id'];
            $buy_number = ($validated['buy_number'] <= 0) ? 1 : $validated['buy_number'];

            $goods = Goods::query()->whereNo($goods_no)->show()->first();

            if (! $goods) {
                throw new BusinessException('商品无效');
            }

            $cart = Cart::query()->whereId($id)->whereUserId($user->id)->whereGoodsId($goods->id)
                ->when($goods_sku_id > 0, fn (Builder $query) => $query->whereGoodsSkuId($goods_sku_id))
                ->first();

            if (! $cart) {
                throw new BusinessException('购物车商品不存在');
            }

            if ($goods->total <= 0 || $goods->total < $buy_number) {
                throw new BusinessException('【'.$goods->name.'】库存不足');
            }

            if ($goods->can_quota == Goods::QUOTA && $goods->quota_number < $buy_number) {
                throw new BusinessException('【'.$goods->name.'】超出限购数量：'.$goods->quota_number);
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
        $user = get_user();

        try {
            $validated = $request->validate([
                'goods_no' => 'required|string',
                'goods_sku_id' => 'required|int',
                'is_check' => 'required|int|in:0,1',
            ], [], [
                'goods_no' => '商品编号',
                'goods_sku_id' => '商品规格ID',
                'is_check' => '是否选中结算',
            ]);

            $goods_no = $validated['goods_no'];
            $goods_sku_id = $validated['goods_sku_id'];
            $is_check = $validated['is_check'];

            if (! $goods_no) {
                // goods_no不存在 代表：全选/全不选
                $flag = Cart::query()->whereUserId($user->id)->update(['is_check' => $is_check]);

                if (! $flag) {
                    throw new BusinessException('变更选中失败');
                }
            } else {
                // goods_no存在 代表：单个商品
                $goods = Goods::query()->whereNo($goods_no)->show()->first();

                if (! $goods) {
                    throw new BusinessException('商品无效');
                }

                $cart = Cart::query()->whereUserId($user->id)->whereGoodsId($goods->id)
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

    /**
     * 清空失效商品.
     */
    public function emptyInvalid(CartDao $cart_dao)
    {
        $user = get_user();

        try {
            $data = $cart_dao->cartGoodsList($user);
            $invalid_carts = $data['invalid_carts'];

            if (! count($invalid_carts)) {
                throw new BusinessException('暂无失效商品');
            }

            if (! Cart::whereUserId($user->id)->whereIn('id', collect($invalid_carts)->pluck('id'))->delete()) {
                throw new BusinessException('清空失败');
            }

            return $this->success('清空成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('清空失效商品异常~');
        }
    }

    /**
     * 移入收藏.
     */
    public function moveCollect(Request $request)
    {
        $user = get_user();

        try {
            $validated = $request->validate([
                'ids' => 'required|array',
            ], [], [
                'ids' => '购物车ID',
            ]);

            $ids = $validated['ids'];

            DB::beginTransaction();

            $flag1 = $flag2 = false;

            foreach ($ids as $id) {
                $cart = Cart::whereUserId($user->id)->whereId($id)->first();

                if ($cart) {
                    $flag1 = GoodsCollect::updateOrCreate(['user_id' => $user->id, 'goods_id' => $cart->goods_id], ['updated_at' => date('Y-m-d H:i:s'), 'is_attention' => GoodsCollect::ATTENTION_YES]);

                    if ($flag1) {
                        if ($cart->delete()) {
                            $flag2 = true;
                        }
                    }
                }
            }

            if ($flag1 && $flag2) {
                DB::commit();

                return $this->success('操作成功');
            }
            DB::rollBack();

            return $this->error('操作失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('移入收藏异常~');
        }
    }

    /**
     * 去结算.
     */
    public function placeOrder()
    {
        $user = get_user();

        try {
            // 获取可以结算的商品数据
            $carts = Cart::query()->whereUserId($user->id)
                ->whereHas('goods', function ($query) {
                    $query->show();
                })
                ->whereIsCheck(Cart::IS_CHECK_YES)
                ->orderByDesc('updated_at')->orderByDesc('id')
                ->get();

            if (! $carts->count()) {
                throw new BusinessException('没有待结算有效商品');
            }

            foreach ($carts as $cart) {
                $goods = $cart->goods;

                if ($goods->total <= 0 || $goods->total < $cart->buy_number) {
                    throw new BusinessException('【'.$goods->name.'】库存不足');
                }

                if ($goods->can_quota == Goods::QUOTA && $goods->quota_number < $cart->buy_number) {
                    throw new BusinessException('【'.$goods->name.'】超出限购数量：'.$goods->quota_number);
                }
            }

            return $this->success('允许结算');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('去结算异常~');
        }
    }
}
