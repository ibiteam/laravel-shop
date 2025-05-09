<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsSpecValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class CartDao
{
    /**
     * 获取指定用户的购物车商品列表.
     *
     * @throws BusinessException
     */
    public function cartGoodsList(User $user): array
    {
        try {
            $carts = Cart::query()->whereUserId($user->id)
                ->with(['goods' => function ($query) {
                    $query->withTrashed();  // 包括已软删除的商品
                }])
                ->with(['goods.skus', 'goodsSku'])
                ->orderByDesc('updated_at')->orderByDesc('id')
                ->get(['id', 'goods_id', 'goods_sku_id', 'buy_number', 'is_check']);

            $validCarts = [];   // 有效的购物车记录
            $invalidCarts = []; // 无效的购物车记录
            $total = [
                'check_count' => 0,
                'total_price_format' => 0, // 有格式
                'total_price' => 0, // 总价格 无格式
                'total_integral' => 0, // 总积分
            ];

            foreach ($carts as $cart) {
                if (! $cart->goods) {// 商品不存在
                    continue;
                }

                $goods_price = $cart->goods->price;
                $goods_integral = $cart->goods->integral;

                if ($cart->goods_sku_id && $cart->goodsSku) {
                    $goods_price = $cart->goodsSku->price;
                    $goods_integral = $cart->goodsSku->integral;
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
                            'no' => $cart->goods->no,
                            'name' => $cart->goods->name,
                            'image' => $cart->goods->image,
                            'price' => $goods_price,
                            'unit' => $cart->goods->unit,
                            'integral' => $goods_integral,
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

                    // 商品规格描述
                    $sku_desc = '';

                    if ($cart->goods_sku_id && $cart->goodsSku) {
                        GoodsSpecValue::whereIn('id', explode('|', $cart->goodsSku->sku_value))->with('spec')->get()->map(function ($item) use (&$sku_desc) {
                            $sku_desc .= $item->spec->name.':'.$item->value.';';
                        });
                    }

                    // 限购数量校验
                    $buy_number = $cart->buy_number;

                    if ($cart->goods->can_quota && $cart->goods->quota_number > 0) {
                        if ($buy_number > $cart->goods->quota_number) {
                            $buy_number = $cart->goods->quota_number;
                        }
                    }

                    if ($cart->is_check == Cart::IS_CHECK_YES) {
                        $total['check_count']++;
                        $total['total_price'] += $goods_price * $buy_number;
                        $total['total_integral'] += $goods_integral;
                    }

                    $validCarts[] = [
                        'id' => $cart->id,
                        'buy_number' => $buy_number,
                        'is_check' => $cart->is_check,
                        'goods_sku_id' => $cart->goods_sku_id,
                        'goods' => [
                            'no' => $cart->goods->no,
                            'name' => $cart->goods->name,
                            'image' => $cart->goods->image,
                            'price' => $goods_price,
                            'unit' => $cart->goods->unit,
                            'total' => $cart->goods->total,
                            'integral' => $goods_integral,
                            'can_quota' => $cart->goods->can_quota,
                            'quota_number' => $cart->goods->quota_number,
                            'sku_desc' => $sku_desc,
                        ],
                    ];
                }
            }

            $total['total_price_format'] = price_number_format(max($total['total_price'], 0));
            $total['total_price'] = to_number_format(max($total['total_price'], 0));

            return [
                'valid_carts' => $validCarts,
                'invalid_carts' => $invalidCarts,
                'total' => $total,
            ];
        } catch (\Exception $e) {
            throw new BusinessException('查询购物车商品列表异常'.$e->getMessage());
        }
    }

    /**
     * 获取购物车有效商品数量.
     */
    public function getValidCarNumber(int $user_id): int
    {
        return Cart::query()->whereUserId($user_id)
            ->whereHas('goods', function ($query) {
                $query->show();
            })->count();
    }

    /**
     * 获取当前用户的下单商品
     *
     * @return EloquentCollection<int,Cart>|Collection<int,Cart>
     */
    public function getDoneCartGoods(int $user_id): EloquentCollection|Collection
    {
        return Cart::query()->with(['goods', 'goods.skus'])->whereUserId($user_id)->whereIsCheck(Cart::IS_CHECK_YES)->get();
    }

    /**
     * 清除购物车中当前用户指定ID的商品
     */
    public function clearDoneCartGoods(array $destroy_ids, int $user_id): void
    {
        Cart::query()->whereUserId($user_id)->whereIn('id', $destroy_ids)->delete();
    }
}
