<?php

namespace App\Http\Dao;

use App\Models\UserSearch;

class UserSearchDao
{
    /**
     * 用户搜索记录.
     */
    public function add(int $user_id, string $keywords, array $goods_ids): bool
    {
        if (count($goods_ids) > 30) {
            $goods_ids = array_slice($goods_ids, 0, 30);
        }

        $user_search = new UserSearch;
        $user_search->user_id = $user_id;
        $user_search->keywords = $keywords;
        $user_search->goods_ids = implode(',', $goods_ids);
        $user_search->source = get_source();

        return $user_search->save();
    }
}
