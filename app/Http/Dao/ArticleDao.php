<?php

namespace App\Http\Dao;

use App\Exceptions\BusinessException;
use App\Models\Article;
use App\Models\User;

class ArticleDao
{
    /**
     * 获取文章详情.
     *
     * @throws BusinessException
     */
    public function getArticleById(int $article_id, ?User $user)
    {
        $article = Article::query()
            ->with(['articleContent' => function ($query) {
                $query->select('article_id', 'content');
            }])
            ->select(['id', 'title', 'cover', 'description', 'keywords', 'author', 'is_login', 'updated_at'])
            ->whereId($article_id)
            ->whereIsShow(Article::IS_SHOW_YES)
            ->first();

        if ($article) {
            if (! $user && $article->is_login == Article::IS_LOGIN_YES) {
                throw new BusinessException('文章需要登录才可访问');
            }

            // 文章内容
            $article->content = $article->articleContent->content;
            // 移除 articleContent 关联
            $article->unsetRelation('articleContent');
        }

        return $article;
    }

    /**
     * 获取文章数据（value => label）.
     */
    public function getArticleOptions($keywords, $limit = 10): array
    {
        return Article::query()
            ->select(['id as value', 'title as label'])
            ->whereIsLogin(Article::IS_LOGIN_NO)
            ->whereIsShow(Article::IS_SHOW_YES)
            ->when($keywords, fn ($query) => $query->where(function ($query) use ($keywords) {
                $query->where('id', $keywords)->orWhere('title', 'like', '%'.$keywords.'%');
            }))
            ->orderByDesc('sort')
            ->limit($limit)
            ->get()->map(function ($item) {
                $item->label = $item->label.'【'.$item->value.'】';

                return $item;
            })->toArray();
    }
}
