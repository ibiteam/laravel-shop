<?php

namespace App\Http\Dao;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryDao
{
    /**
     * 构建分类树状结构
     *
     * @param Collection $categories
     * @param int $parentId
     * @return array
     */
    public function categoryTree(Collection $categories, int $parentId = 0): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->categoryTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                } else {
                    $category->children = [];
                }
                $tree[] = $category;
            }
        }

        return $tree;
    }
}
