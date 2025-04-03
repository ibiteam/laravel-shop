<?php

namespace App\Http\Dao;

use App\Models\Category;
use App\Models\Goods;
use App\Models\ShopConfig;

class SearchDao
{
    public const PRICE_DESC = 'price_desc';
    public const PRICE_ASC = 'price_asc';
    public const SALE_DESC = 'sale_desc';

    /**
     * 执行搜索并返回分页结果.
     */
    public function searchGoods(array $params, int $user_id): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
    {
        $params['page'] = max(1, (int)($params['page'] ?? 1));
        $params['number'] = max(1, (int)($params['number'] ?? 15));

        if (!empty($params['min_price'])) {
            $params['min_price'] = max(0, (float)$params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $params['max_price'] = max(0, (float)$params['max_price']);
        }

        if (shop_config(ShopConfig::SEARCH_DRIVER) == 2) {
            // MeiliSearch搜索
            $options = [
                'attributesToSearchOn' => ['name'],
                'filter' => ['status = ' . Goods::STATUS_ON_SALE], // 上架商品
                'sort' => ['sort:desc'], // 默认排序
                'attributesToRetrieve' => ['id', 'name', 'sub_name', 'label', 'price', 'unit', 'image', 'sales_volume'], // 指定返回的字段
            ];

            if (! empty($params['category_id'])) {
                $options['filter'][] = 'category_id = ' . $params['category_id'];
            }

            if (! empty($params['min_price'])) {
                $options['filter'][] = 'price >= ' . $params['min_price'];
            }

            if (! empty($params['max_price'])) {
                $options['filter'][] = 'price <= ' . $params['max_price'];
            }

            if (! empty($params['sort_type'])) {
                $options['sort'] = match ($params['sort_type']) {
                    static::PRICE_ASC => ['price:asc'],
                    static::PRICE_DESC => ['price:desc'],
                    static::SALE_DESC => ['sales_volume:desc'],
                    default => ['sort:desc'],
                };
            }

            // 确保默认排序生效
            if (empty($options['sort'])) {
                $options['sort'] = ['sort:desc'];
            }

            // 确保 attributesToRetrieve 生效
            if (empty($options['attributesToRetrieve'])) {
                $options['attributesToRetrieve'] = ['id', 'name', 'sub_name', 'label', 'price', 'unit', 'image', 'sales_volume'];
            }

            $query = Goods::search($params['keywords'] ?? '')->options($options);

        } else {
            // 数据库搜索

            $query = Goods::query()->show()->select(['id', 'name', 'sub_name', 'label', 'price', 'unit', 'image', 'sales_volume']);

            if (! empty($params['keywords'])) {
                $query->where('name', 'like', '%'.$params['keywords'].'%');
            }

            if (! empty($params['category_id'])) {
                $query->where('category_id', $params['category_id']);
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
        }

        $list = $query->paginate(perPage: $params['number'], page: $params['page']);

        $add_keywords = '';  // 搜索关键词

        if (! empty($params['keywords'])) {
            $add_keywords = $params['keywords'];
        }

        if (! empty($params['category_id'])) {
            if (! $add_keywords) {
                $add_keywords = Category::query()->whereId($params['category_id'])->value('name') ?? '';
            }
        }

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
