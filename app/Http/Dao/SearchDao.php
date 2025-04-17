<?php

namespace App\Http\Dao;

use App\Models\Category;
use App\Models\Goods;
use App\Models\ShopConfig;
use App\Utils\Constant;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchDao
{
    public const TIME_DESC = 'time_desc';
    public const SALE_DESC = 'sale_desc';
    public const PRICE_DESC = 'price_desc';
    public const PRICE_ASC = 'price_asc';

    /**
     * 执行搜索并返回分页结果.
     */
    public function searchGoods(array $params, int $user_id, int $is_record_search = Constant::ONE): LengthAwarePaginator
    {
        $params['page'] = max(1, (int) ($params['page'] ?? 1));
        $params['number'] = max(1, (int) ($params['number'] ?? 15));

        if (intval(shop_config(ShopConfig::SEARCH_DRIVER)) == 2) {
            // MeiliSearch搜索
            $options = [
                'attributesToSearchOn' => ['name'],
                'filter' => ['status = '.Goods::STATUS_ON_SALE], // 上架商品
                'sort' => ['sort:desc'], // 默认排序
            ];

            if (! empty($params['category_id'])) {
                $options['filter'][] = 'category_id = '.$params['category_id'];
            }

            if (! empty($params['sort_type'])) {
                $options['sort'] = match ($params['sort_type']) {
                    static::TIME_DESC => ['created_at:desc'],
                    static::SALE_DESC => ['sales_volume:desc'],
                    static::PRICE_ASC => ['price:asc'],
                    static::PRICE_DESC => ['price:desc'],
                    default => ['sort:desc'],
                };
            }

            $query = Goods::search($params['keywords'] ?? '')->options($options);
        } else {
            // 数据库搜索
            $query = Goods::query()->show();

            if (! empty($params['keywords'])) {
                $query->where('name', 'like', '%'.$params['keywords'].'%');
            }

            if (! empty($params['category_id'])) {
                $query->where('category_id', $params['category_id']);
            }

            if (! empty($params['sort_type'])) {
                switch ($params['sort_type']) {
                    case static::TIME_DESC:
                        $query->orderBy('created_at', 'desc');

                        break;

                    case static::SALE_DESC:
                        $query->orderBy('sales_volume', 'desc');

                        break;

                    case static::PRICE_ASC:
                        $query->orderBy('price', 'asc');

                        break;

                    case static::PRICE_DESC:
                        $query->orderBy('price', 'desc');

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
        if ($is_record_search) {
            $this->addUserKeyword($user_id, $add_keywords, $list->pluck('id')->toArray());
        }

        // 只返回指定字段
        $list->transform(function ($item) {
            return [
                'no' => $item->no,
                'category_id' => $item->category_id,
                'name' => $item->name,
                'sub_name' => $item->sub_name,
                'label' => $item->label,
                'price' => $item->price,
                'unit' => $item->unit,
                'integral' => $item->integral,
                'image' => $item->image,
                'sales_volume' => $item->sales_volume,
                'created_at' => $item->created_at,
            ];
        });

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
