<?php

namespace App\Http\Dao;

use App\Models\ArticleCategory;

class ArticleCategoryDao
{
    /**
     * 获取所有树状分类.
     */
    public function getTreeList()
    {
        return ArticleCategory::query()->with('allChildren')
            ->whereParentId(0)
            ->orderByDesc('sort')
            ->get()->map(fn (ArticleCategory $article_category) => $this->articleCategoryFormat($article_category));
    }

    /**
     * 获取显示的分类.
     */
    public function getShowTreeList()
    {
        return ArticleCategory::query()->with('showAllChildren')
            ->whereParentId(0)
            ->whereIsShow(ArticleCategory::IS_SHOW_YES)
            ->orderByDesc('sort')
            ->get()->map(fn (ArticleCategory $article_category) => $this->showArticleCategoryFormat($article_category));
    }

    /**
     * 递归处理树状结构数据.
     */
    private function articleCategoryFormat(ArticleCategory $article_category): array
    {
        if ($article_category->allChildren->isEmpty()) {
            return [
                'id' => $article_category->id,
                'parent_id' => $article_category->parent_id,
                'name' => $article_category->name,
            ];
        }

        return [
            'id' => $article_category->id,
            'parent_id' => $article_category->parent_id,
            'name' => $article_category->name,
            'children' => $article_category->allChildren->map(fn (ArticleCategory $article_category) => $this->articleCategoryFormat($article_category))->toArray(),
        ];
    }

    private function showArticleCategoryFormat(ArticleCategory $article_category): array
    {
        if ($article_category->showAllChildren->isEmpty()) {
            return [
                'id' => $article_category->id,
                'parent_id' => $article_category->parent_id,
                'name' => $article_category->name,
            ];
        }

        return [
            'id' => $article_category->id,
            'parent_id' => $article_category->parent_id,
            'name' => $article_category->name,
            'children' => $article_category->showAllChildren->map(fn (ArticleCategory $article_category) => $this->showArticleCategoryFormat($article_category))->toArray(),
        ];
    }
}
