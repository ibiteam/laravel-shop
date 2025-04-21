<?php

namespace App\Http\Dao;

use App\Models\SearchKeyword;

class SearchKeywordDao
{
    // 添加关键词搜索记录
    public function add(string $keywords): bool
    {
        if (mb_strlen($keywords) >= 20 || str_contains($keywords, 'select') || str_contains($keywords, '$')) {
            return false;
        }

        $search_keywords = SearchKeyword::whereKeywords($keywords)->first();

        if ($search_keywords) {
            $search_keywords->increment('count'); // 自增1
        } else {
            $search_keywords = new SearchKeyword;
            $search_keywords->keywords = $keywords;
            $search_keywords->count = 1;
            $search_keywords->save();
        }

        return true;
    }

    // 获取搜索关键词
    public function getKeywords(string $keywords): array
    {
        return SearchKeyword::query()->select('keywords')
            ->where('keywords', 'like', "%{$keywords}%")
            ->whereIsCanSearch(SearchKeyword::CAN_SEARCH_YES)
            ->limit(10)
            ->orderByDesc('count')->get()->pluck('keywords')->toArray();
    }
}
