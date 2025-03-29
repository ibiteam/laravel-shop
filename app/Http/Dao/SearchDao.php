<?php

namespace App\Http\Dao;

use App\Models\Category;
use App\Models\Goods;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchDao
{

    public const PRICE_DESC = 'price_desc';
    public const PRICE_ASC = 'price_asc';
    public const SALE_DESC = 'sale_desc';

    /**
     * 执行搜索并返回分页结果.
     */
    public function searchGoods(array $params, int $user_id): LengthAwarePaginator
    {
        $query = Goods::query()->show() // 只查询上架且审核通过的商品
            ->select(['id', 'name', 'sub_name', 'label', 'price', 'unit', 'image', 'sales_volume']);

        $add_keywords = '';

        if (! empty($params['keywords'])) {
            $query->where('name', 'like', '%'.$params['keywords'].'%');
            $add_keywords = $params['keywords'];
        }

        if (! empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);

            if (! $add_keywords) {
                $add_keywords = Category::query()->whereId($params['category_id'])->value('name') ?? '';
            }
        }

        if (! empty($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }

        if (! empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        if (! empty($params['sort_type'])) {
            switch ($params['sort_type']) {
                case static::PRICE_ASC:
                    $query->orderBy('price', 'asc');
                    break;

                case static::PRICE_DESC:
                    $query->orderBy('price', 'desc');
                    break;

                case static::SALE_DESC:
                    $query->orderBy('sales_volume', 'desc');
                    break;

                default:
                    $query->orderBy('sort', 'desc');
                    break;
            }
        } else {
            $query->orderBy('sort', 'desc');
        }

        $list = $query->paginate($params['number']);

        // 记录用户搜索
        $this->addUserKeyword($user_id, $add_keywords, $list->pluck('id')->toArray());

        return $list;
    }

    /**
     * 用户搜索记录.
     */
    public function addUserKeyword($user_id, $keywords, $goods_ids): bool
    {
        if (is_spider() || ! $keywords || count($goods_ids) <= 0) {
            return false;
        }

        // 添加用户搜索记录
        app(UserSearchDao::class)->add($user_id, $keywords, $goods_ids);

        // 添加关键词搜索记录
        app(SearchKeywordDao::class)->add($keywords);

        return true;
    }
}
