<?php

namespace App\Services;

use App\Models\AppWebsiteDecoration;
use App\Models\Category;
use App\Models\Good;
use App\Models\SellerShopinfo;
use App\Models\UserSeller;

class RomoteSearchService
{
    public const TYPE_PRICE_INDEX = 1; //价格指数
    public const TYPE_SHOP = 2; //获取店铺信息
    public const TYPE_ARTICLE = 3; //文章信息
    public const TYPE_ZIXUN_PRODUCE = 4; //资讯商品涨跌产品
    public const TYPE_ZIXUN_MENU = 5; //资讯分类
    public const TYPE_APP_WEBSITE_INDUSTRIAL = 6; // APP装修页面(产业链合集)
    public const TYPE_APP_WEBSITE_COMMON = 7; // APP装修页面(公共页面（底部菜单）)
    public const TYPE_Video = 8; // 普通视频
    public const TYPE_APP_CATEGORY = 9; // 分类
    public const TYPE_APP_BRAND = 10; // 品牌
    public const TYPE_APP_CHILD_LEVEL_CATE = 11; // 子级分类
    public const TYPE_APP_WEBSITE_CATEGORY = 12; // APP装修页面(分类页面合集)
    public const TYPE_GOODS = 13; // 商品
    public const TYPE_ARTICLE_CAT = 14; // 文章分类
    public const TYPE_ACT_SECKILL_DISCOUNT = 15; // 秒杀、特卖活动数据
    public const TYPE_ACTIVITY_GOODS = 16; // 秒杀、特卖活动商品数据
    public const TYPE_INFO_ARTICLE_CAT = 17; // 资讯文章分类
    public const TYPE_LIVE_SHOW = 18; // 直播观看端
    public const TYPE_ACTIVITY_TEMPLATE = 19; // 获取活动模板
    public const TYPE_ACT_PRESELL = 20; // 预售活动数据
    public const TYPE_ACT_PRESELL_GOODS = 21; // 预售下商品数据
    public const TYPE_ACT_DD_TRY = 22; // 多多试用活动
    public const TYPE_ACT_DD_TRY_GOODS = 23; // 多多试用下商品数据
    public const TYPE_ACT_AUCTION_GOODS = 24; // 竞拍活动下商品数据
    public const TYPE_INTEGRAL_GOODS = 25; // 积分商品
    public const TYPE_INFO_ARTICLE = 27; //资讯文章
    public const TYPE_INVITE_TEMPLATE = 28; //邀约模板
    public const TYPE_APP_WEBSITE_PUBLIC = 29; //APP装修页面(公共页合集)
    public const TYPE_NEW_RED_PACKET = 30; //新年红包
	public const TYPE_LIVE_NOTICE = 31; // 直播预告
	public const TYPE_LIVE_RECORD = 32; // 直播回放
	public const TYPE_LEVEL_ARTICLE_CAT = 36; // 文章分类级联数据
	public const TYPE_TOP_ARTICLE_CAT = 35; // 顶级文章分类
	public const NEW_YEAR_MARKING = 34; // 新年营销搜索
    public const TYPE_ZIXUN_PAGE = 39; // 资讯单页
    public const TYPE_ZIXUN_EXPERT = 38; // 专家库

	public const SELLER_LABLE = 37; // 卖家版-标签栏
	public const YI_QI_XIU = 40; // 易企秀
    public const TYPE_ZIXUN_PRICE_INDEX = 41; // 新价格指数
	public const FREE_TRY_LIST = 42; // 获取试样列表
    public const TYPE_APP_ZIXUN_PAGE = 43; // 移动端资讯单页
    public const TYPE_ZIXUN_AREA = 44; // 资讯地区
    public const TYPE_DD_Video = 45; // 资讯视频



    public $keywords; //关键字
    public $is_edit; //是否是编辑

    public function getOption($page_type = '', $keywords = '', $is_edit = false)
    {
        $this->keywords = $keywords;
        $this->is_edit = $is_edit;
        $result = [];
        switch ($page_type) {
            case self::TYPE_SHOP:
                $result = $this->getShop();

                break;
            case self::TYPE_GOODS:

                $result = $this->getGoods();

                break;
            case self::TYPE_APP_WEBSITE_INDUSTRIAL:
                $result = $this->getAppWebsiteCollectionByAlias(AppWebsiteDecoration::ALIAS_INDUSTRIAL);

                break;
            case self::TYPE_APP_WEBSITE_COMMON:
                $result = $this->getAppWebsiteCommon();

                break;
            case self::TYPE_APP_CATEGORY:

                $result = $this->getAppCategory();

                break;
            case self::TYPE_APP_CHILD_LEVEL_CATE:
                $result = $this->getAppChildLevelCate();

                break;
            case self::TYPE_APP_WEBSITE_CATEGORY:
                $result = $this->getAppWebsiteCollectionByAlias(AppWebsiteDecoration::ALIAS_CATEGORY_LIST);

                break;
            case self::TYPE_APP_WEBSITE_PUBLIC:
                $result = $this->getAppWebsiteCollectionByAlias(AppWebsiteDecoration::ALIAS_PUBLIC);

                break;
			case self::SELLER_LABLE:
				$result = $this->getSellerLable();

				break;
        }

        return $result;
    }

    /**
     * 获取所有子级分类数据
     * @return array
     */
    public function getAppChildLevelCate()
    {
        $parent_id = (int)$this->keywords;
        $one_level = Category::whereAppIsShow(Category::APP_IS_SHOW)
            ->whereParentId($parent_id)
            ->select('cat_id', 'cat_name', 'parent_id', 'logo')
            ->get()->map(function (Category $cate) {
                return [
                    'label' => "【{$cate->cat_id}】{$cate->cat_name}",
                    'value' => $cate->cat_id,
                    'image' => $cate->logo,
                ];
            })->toArray();

        return $one_level;
    }

    /**
     * 获取所有推荐到app的分类数据
     * @return array
     */
    public function getAppCategory()
    {
        $cate = Category::query()
            ->whereAppIsShow(Category::APP_IS_SHOW)
            ->when(is_numeric($this->keywords), function ($id_query) {
                return $id_query->where('cat_id', $this->keywords);
            })->when(!is_numeric($this->keywords), function ($name_query) {
                return $name_query->where('cat_name', 'LIKE', "%{$this->keywords}%");
            })->select('cat_id', 'cat_name', 'parent_id', 'logo')
            ->get()->map(function (Category $cate) {
                return [
                    'label' => "【{$cate->cat_id}】{$cate->cat_name}",
                    'value' => $cate->cat_id,
                    'parent_id' => $cate->parent_id,
                    'cat_name' => $cate->cat_name,
                    'image' => $cate->logo,
                ];
            })->toArray();

        return $this->keywords ? $cate : category_tree($cate);
    }

    /**
     * 获取APP装修公共页面底部菜单中的数据
     * @return array
     */
    public function getAppWebsiteCommon()
    {
		if($this->is_edit){
			if(is_array($this->keywords)){
				$query = AppWebsiteDecoration::query()->whereIn('id',$this->keywords);
			}else{
				$query = AppWebsiteDecoration::query()->where('id',$this->keywords);
			}
		}else{
			$query = AppWebsiteDecoration::query()->when(is_numeric($this->keywords), function ($id_query) {
				return $id_query->where('id', $this->keywords);
			})->when(!is_numeric($this->keywords), function ($name_query) {
				return $name_query->where('name', 'LIKE', "%{$this->keywords}%");
			});
		}
        return $query->whereVersion(AppWebsiteDecoration::VERSION_BUYER)->where(function ($not_query) {
            return $not_query->where('alias','!=', AppWebsiteDecoration::ALIAS_PUBLIC)->orWhere(function ($not_alias_query) {
                return $not_alias_query->where('alias', '=', AppWebsiteDecoration::ALIAS_PUBLIC)->where('parent_id', '!=', 0);
            });
        })->whereJsonContains('type',AppWebsiteDecoration::TYPE_BOTTOM_MENU)
        ->get()->map(function (AppWebsiteDecoration $appWebsiteDecoration) {
                return [
                    'label' => "【{$appWebsiteDecoration->id}】{$appWebsiteDecoration->name}",
                    'value' => $appWebsiteDecoration->id,
                ];
            })->toArray();
    }

    /**
     * 获取APP装修合集数据(根据别名）.
     *
     * @return array
     */
    public function getAppWebsiteCollectionByAlias($alias)
    {
        if ($this->is_edit) {
            if (is_array($this->keywords)) {
                $query = AppWebsiteDecoration::query()->whereIn('id', $this->keywords);
            } else {
                $query = AppWebsiteDecoration::query()->where('id', $this->keywords);
            }
        } else {
            $query = AppWebsiteDecoration::query()->when(is_numeric($this->keywords), function ($id_query) {
                return $id_query->where('id', $this->keywords);
            })->when(!is_numeric($this->keywords), function ($name_query) {
                return $name_query->where('name', 'LIKE', "%{$this->keywords}%");
            });
        }

        return $query
            ->whereVersion(AppWebsiteDecoration::VERSION_BUYER)
            ->whereAlias($alias)
            ->childLevel()
            ->get()->map(function (AppWebsiteDecoration $appWebsiteDecoration) {
                return [
                    'label' => "【{$appWebsiteDecoration->id}】{$appWebsiteDecoration->name}",
                    'value' => $appWebsiteDecoration->id,
                ];
            })->toArray();
    }

    public function getCatList($keywords)
    {
        if(!$keywords){
            return [];
        }
        $cate = Category::query()
            ->whereAppIsShow(Category::APP_IS_SHOW)
            ->when(is_numeric($keywords), function ($id_query) use($keywords) {
                return $id_query->where('cat_id', $keywords);
            })->when(!is_numeric($keywords), function ($name_query) use($keywords) {
                return $name_query->where('cat_name', 'LIKE', "%{$keywords}%");
            })->select('cat_id', 'cat_name', 'parent_id')
            ->get()->map(function (Category $cate) {
                return [
                    'label' => "【{$cate->cat_id}】{$cate->cat_name}",
                    'value' => $cate->cat_id,
                    'parent_id' => $cate->parent_id,
                    'cat_name' => $cate->cat_name,
                ];
            })->toArray();

        return $cate;
    }

    /**
     * 获取店铺.
     *
     * @param $keywords
     *
     * @return array
     */
    private function getShop()
    {
        $keywords = $this->keywords;
        if (!$keywords) {
            return [];
        }
        if ($this->is_edit) {
            $query = new SellerShopinfo();
            if (is_array($keywords)) {
                $query = $query->whereIn('seller_id', $keywords);
            } else {
                $query = $query->where('seller_id', $keywords);
            }
        } else {
            $query = SellerShopinfo::where(function ($query) use ($keywords) {
                $query->orWhere('shop_name', 'like', '%'.$keywords.'%')->orWhere('seller_id', $keywords);
            })->limit(10);
        }

        return $query->whereStatus(SellerShopinfo::STATUS_OPEN)->whereHas('user_seller', function ($query) {
            $query->whereIsCheck(UserSeller::CHECKED);
        })->select('seller_id', 'shop_name')->orderByDesc('id')->get()->map(function (SellerShopinfo $sellerShopinfo) {
            return [
                'label' => '【'.$sellerShopinfo->seller_id.'】'.$sellerShopinfo->shop_name,
                'value' => $sellerShopinfo->seller_id,
                'name' => $sellerShopinfo->shop_name
            ];
        })->toArray();
    }

    /**
     * 获取商品列表.
     * keywords 可传入形式
	 * string (商品名称|id)  is_edit为false时
	 * array['keywords'=>'商品名称|id','is_ziying'=>'','cat_id'=>10]）  is_edit为false时
	 * array（[1044,1034]   is_edit为true时
     * @return array
     */
    private function getGoods($act_type = '')
    {
		//keywords 可传入多种形式 见上方参数说明
		$keywords = $this->keywords;
        if ($this->is_edit) {
            $query = new Good();
            if (is_array($keywords)) {
                $query = $query->whereIn('goods_id', $keywords);
            } else {
                $query = $query->where('goods_id', $keywords);
            }
        } else {
			if (is_array($keywords)) {
				return [];
			}
            $query = Good::where(function ($query) use ($keywords) {
                $query->orWhere('goods_name', 'like', '%'.$keywords.'%')->orWhere('goods_id', 'like', '%'.$keywords.'%')->orWhere('goods_sn', $keywords);
            })->limit(10);
			$is_ziying = request()->get('is_ziying')??'';
			$cat_id = request()->get('cat_id')??0;
			if ($cat_id) {
				$cat_ids = explode(',',app(CategoryDao::class)->gatCatId($cat_id) . ',' . $cat_id);   // 添加该分类本身
				$query = $query->whereIn('cat_id',$cat_ids);
			}
			if ($is_ziying) {
				$query = $query->whereHas('shopInfo', function ($query) use ($is_ziying) {
					if (1 == $is_ziying) {
						$query->whereIsZiying(SellerShopinfo::ZIYING);
					} elseif (2 == $is_ziying) {
						$query->whereIsZiying(SellerShopinfo::OTHER);
					}

					return $query;
				});
			}
        }

        if (FavourableActivity::TRY == $act_type) {
            $query->where('extension_code', 1)->OnShow();
        } elseif (FavourableActivity::INTEGRAL_GOODS == $act_type) {
            $query->where('extension_code', 'score')->whereIsOnSale(\App\Utils\Constant::ONE);
        } else {
            $query->OnShow();
        }
        return $query->whereHas('shopInfo', function ($query) {
            $query->whereStatus(SellerShopinfo::STATUS_OPEN)->whereHas('user_seller', function ($query) {
                $query->whereIsCheck(UserSeller::CHECKED);
            });
        })->select('goods_id', 'goods_name')->orderByDesc('goods_id')->get()->map(function (Good $good) {
            return [
                'label' => '【'.$good->goods_id.'】'.$good->goods_name,
                'value' => $good->goods_id,
            ];
        })->toArray();
    }

	/**
	 * 获取卖家版 底部标签列表
	 * @return array
	 */
	public function getSellerLable()
	{
		if($this->is_edit){
			if(is_array($this->keywords)){
				$query = AppWebsiteDecoration::query()->whereIn('id',$this->keywords);
			}else{
				$query = AppWebsiteDecoration::query()->where('id',$this->keywords);
			}
		}else{
			$query = AppWebsiteDecoration::query()->when(is_numeric($this->keywords), function ($id_query) {
				return $id_query->where('id', $this->keywords);
			})->when(!is_numeric($this->keywords), function ($name_query) {
				return $name_query->where('name', 'LIKE', "%{$this->keywords}%");
			});
		}
		return $query->whereVersion(AppWebsiteDecoration::VERSION_SELLER)->whereJsonContains('type',AppWebsiteDecoration::TYPE_BOTTOM_MENU)
			->get()->map(function (AppWebsiteDecoration $appWebsiteDecoration) {
				return [
					'label' => "【{$appWebsiteDecoration->id}】{$appWebsiteDecoration->name}",
					'value' => $appWebsiteDecoration->id,
				];
			})->toArray();
	}
}
