<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\Cart;
use App\Models\Goods;

class CartDao
{
    /**
     * 获取指定用户的购物车商品列表.
     *
     * @throws BusinessException
     */
    public function cartGoodsList(int $user_id): array
    {
        try {
            $carts = Cart::query()->whereUserId($user_id)
                ->with(['goods' => function ($query) {
                    $query->withTrashed();  // 包括已软删除的商品
                }], 'goodsSku')
                ->orderByDesc('updated_at')->orderByDesc('id')
                ->get(['id', 'goods_id', 'goods_sku_id', 'buy_number', 'is_check']);

            $validCarts = [];   // 有效的购物车记录
            $invalidCarts = []; // 无效的购物车记录

            foreach ($carts as $cart) {
                if (! $cart->goods) {// 商品不存在
                    continue;
                }

                $goods_price = $cart->goods->price;

                if ($cart->goods_sku_id) {
                    $goods_price = $cart->goodsSku ? $cart->goodsSku->price : $goods_price;
                }

                $invalid_type = '';  // 无效类型

                if ($cart->goods->status === Goods::STATUS_NOT_SALE || $cart->goods->deleted_at) {
                    // 商品下架/删除
                    $invalid_type = Cart::INVALID_TYPE_NOT_SALE;
                } elseif ($cart->goods->total <= 0) {
                    // 商品无货
                    $invalid_type = Cart::INVALID_TYPE_OUT_STOCK;
                }

                if ($invalid_type) {
                    // 无效购物车记录
                    $invalidCarts[] = [
                        'id' => $cart->id,
                        'buy_number' => $cart->buy_number,
                        'goods' => [
                            'id' => $cart->goods->id,
                            'name' => $cart->goods->name,
                            'image' => $cart->goods->image,
                            'price' => $goods_price,
                            'unit' => $cart->goods->unit,
                            'invalid_type' => $invalid_type,
                        ],
                    ];
                } else {
                    // 有效购物车记录

                    // 有规格变无规格，保留一个，清空购物车中规格参数
                    if ($cart->goods_sku_id && empty($cart->goods->skus ?? [])) {
                        $cart->goods_sku_id = 0;

                        if ($cart->save()) {
                            // 删除其它有规格的商品
                            $delete_ids = collect($carts)->where('goods_id', $cart->goods_id)->where('id', '<>', $cart->id)->where('goods_sku_id', '>', 0)->pluck('id')->toArray();

                            if (! empty($delete_ids)) {
                                Cart::whereIn('id', $delete_ids)->delete();
                            }
                        }
                    }

                    $validCarts[] = [
                        'id' => $cart->id,
                        'buy_number' => $cart->buy_number,
                        'is_check' => $cart->is_check,
                        'goods_sku_id' => $cart->goods_sku_id,
                        'goods' => [
                            'id' => $cart->goods->id,
                            'name' => $cart->goods->name,
                            'image' => $cart->goods->image,
                            'price' => $goods_price,
                            'unit' => $cart->goods->unit,
                            'skus' => $cart->goods->skus ?? [],
                        ],
                    ];
                }
            }

            return [
                'valid_carts' => $validCarts,
                'invalid_carts' => $invalidCarts,
            ];
        } catch (\Exception $e) {
            throw new BusinessException('购物车商品列表查询异常');
        }
    }

    /**
     * 获取购物车有效商品数量.
     */
    public function getValidCarNumber($user_id): int
    {
        return Cart::query()->whereUserId($user_id)
            ->whereHas('goods', function ($query) {
                $query->show();
            })->count();
    }
}
