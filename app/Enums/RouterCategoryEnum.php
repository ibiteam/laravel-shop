<?php

namespace App\Enums;

enum RouterCategoryEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::BASE_LINK => '基础链接',
            self::CUSTOM_LINK => '自定义链接',
            self::GOODS_LINK => '商品链接',
            self::ARTICLE_LINK => '文章链接',
            self::GOODS_CATEGORY => '商品分类',
            self::GOODS_LIST => '商品',
            self::ARTICLE_CATEGORY => '文章分类',
            self::ARTICLE_LIST => '文章',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?string $source): self
    {
        return match ($source) {
            'base_link' => self::BASE_LINK,
            'custom_link' => self::CUSTOM_LINK,
            'goods_link' => self::GOODS_LINK,
            'article_link' => self::ARTICLE_LINK,
            'goods_category' => self::GOODS_CATEGORY,
            'goods_list' => self::GOODS_LIST,
            'article_category' => self::ARTICLE_CATEGORY,
            'article_list' => self::ARTICLE_LIST,
        };
    }

    case BASE_LINK = 'base_link';  // 基础链接
    case CUSTOM_LINK = 'custom_link';  // 自定义链接
    case GOODS_LINK = 'goods_link';  // 商品链接
    case ARTICLE_LINK = 'article_link';  // 文章链接

    case GOODS_CATEGORY = 'goods_category';  // 商品分类
    case GOODS_LIST = 'goods_list';  // 商品
    case ARTICLE_CATEGORY = 'article_category';  // 文章分类
    case ARTICLE_LIST = 'article_list';  // 文章
}
