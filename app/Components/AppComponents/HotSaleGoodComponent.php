<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\FavourableGoodsDao;
use App\Http\Daos\SellerRankDao;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Category;
use App\Models\Good;
use App\Models\Router;
use App\Models\ShopConfig;
use App\Services\MobileRouterService;
use App\Services\RomoteSearchService;
use App\Utils\Constant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

/**
 * 热销商品组件.
 */
class HotSaleGoodComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => '热销商品',
            'component_name' => $this->getComponentName(),
            'limit' => -1,
            'icon' => '&#xe800;',
            'sort' => '',
        ];
    }

    /**
     * init left and right form data.
     * {@inheritDoc}
     */
    public function parameter(): array
    {
        return [
            'id' => '',
            'name' => '热销商品',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => Constant::ZERO,
            'content' => [
                'title_is_show' => Constant::ONE,
                'icon' => 'https://cdn.toodudu.com/uploads/2024/01/12/组 1@2x.png',
                'title' => '',
                'url' => [  // url链接
                    'alias' => 'https',  // 别名 如：article_detail
                    'value' => '', // alias对应的参数值 || https对应的地址
                    'default_selection_data' => [], // 选中的数据
                ],
                'items' => [
                    [
                        'cat_id' => '',
                        'alias' => '',
                        'sort' => '',
                        'default_categories' => [],
                        'goods_items' => [
                            [
                                'goods_id' => '',
                                'sort' => '',
                                'default_selection_data' => [], // 选中的数据
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * obtain display data
     * {@inheritDoc}
     */
    public function getContent(array $data): array
    {
        $content = $data['content'] ?? [];
        $user_id = $data['user']->user_id ?? 0;
        $source = $data['source'] ?? '';
        $category_list = []; // 获取分类列表
        $good_list = []; // 获取商品数据

        if($content['items']){
            // 获取所有分类列表
            $cat_ids = collect($content['items'])->sortByDesc('sort')->pluck('cat_id')->toArray();
            Category::whereAppIsShow(Category::APP_IS_SHOW)->whereIn('cat_id', $cat_ids)
                ->orderByRaw('FIND_IN_SET(cat_id,?)', [implode(',', $cat_ids)])->get()->map(function (Category $item) use (&$category_list, $content) {
                    $category_list[] = [
                        'cat_name' => collect($content['items'])->where('cat_id', $item->cat_id)->first()['alias'] ?? '',
                        'cat_id' => $item['cat_id'],
                    ];
                });
            // 获取第一个分类下的商品列表
            $first_cat_id = $category_list[0]['cat_id'] ?? 0;
            if ($first_cat_id) {
                $good_list = $this->getGoodInfo($content,$first_cat_id,$user_id);
            }
        }

        // 处理跳转地址
        $url = '';
        if ($content['url']['alias'] && $content['title_is_show']) {
            $mobileRouterService = new MobileRouterService();
            $url = $mobileRouterService->handleUrl($content['url']['alias'], $content['url']['value'] ?? '', $source);
        }

        return [
            'component_name' => $this->getComponentName(),
            'hot_sale_items' => [
                'id' =>$data['id']??'',
                'title_is_show' => $content['title_is_show'] ?? Constant::TRUE,
                'icon' => $content['icon'] ?? '',
                'title' => $content['title'] ?? '',
                'url' => $url,
                'url_alias' => $content['url']['alias'],
                'category_list' => $category_list,
                'goods' => $good_list,
            ],
            'sort' => $data['sort'] ?? 0,
        ];
    }

    /**
     * @param $goods_item
     * @param int $user_id
     * @return array
     */
    public function getGoodInfo($content,int $cat_id,int $user_id):array
    {
        $goods_item = collect($content['items']??[])->where('cat_id', $cat_id)->first()['goods_items'] ?? [];
        $goods_ids = collect($goods_item)->sortByDesc('sort')->pluck('goods_id')->toArray();
        if(!$goods_ids){
            return [];
        }
        // 商品列表
        $goods = Good::whereIn('goods_id', $goods_ids)->onShow()
            ->when(is_harmony_request(), fn (Builder $builder) => $builder->where('is_set_sku', Good::NOT_IS_SET_SKU))
            ->orderByRaw('FIND_IN_SET(goods_id,?)', [implode(',', $goods_ids)])->get();
        if(!$goods){
            return [];
        }
        $sellerRankDao = app(SellerRankDao::class);
        $ziying_sign = shop_config(ShopConfig::IS_SHOW_ZIYING);
        $no_ziying_sign = shop_config(ShopConfig::IS_SHOW_CROP);
        $is_show_attribute_source = shop_config(ShopConfig::IS_SHOW_ATTRIBUTE_SOURCE);
        return $goods->map(function (Good $good) use ($user_id, $sellerRankDao, $ziying_sign, $no_ziying_sign,$is_show_attribute_source) {
            $good_info = app(FavourableGoodsDao::class)->goodsActivityPrice($good, $sellerRankDao, $user_id);
            $good_list['goods_id'] = $good->goods_id;
            $good_list['goods_name'] = $good_info->goods_name;
            $good_list['goods_thumb'] = $good->goods_thumb;
            $good_list['shop_price'] = get_new_price($good_info->shop_price);
            $good_list['format_shop_price'] = price_format(get_new_price($good_info->shop_price));
            $good_list['unit'] = $good_info->unit;
            $is_ziying = $good->shopInfo->is_ziying ?? 0;
            $good_list['is_ziying'] = $is_ziying;
            $good_list['sign'] = '';
            if ($is_ziying && $ziying_sign) {
                $good_list['sign'] = $ziying_sign;
            }
            if (!$is_ziying && $no_ziying_sign) {
                $good_list['sign'] = $no_ziying_sign;
            }
            $attribute_source = $good->attribute_source;
            if(!$is_show_attribute_source){
                $attribute_source = '';
            }
            $good_list['attribute_source'] = $attribute_source;
            $good_list['goods_type'] = '';
            if($attribute_source==Good::ATTRIBUTE_SOURCE_XH){
                $good_list['goods_type'] = '现货';
            }elseif ($attribute_source==Good::ATTRIBUTE_SOURCE_QH) {
                $good_list['goods_type'] = '期货';
            }
            return $good_list;
        })->toArray();
    }


    /**
     * display 与 parameter 中的格式一致 只多了一个 content_data 用来展示画布上的数据
     * {@inheritDoc}
     */
    public function display(array $data = []): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];
        $romote_search_service = new RomoteSearchService();
        $mobile_router_service = new MobileRouterService();
        $content['url']['default_selection_data'] = $mobile_router_service->getOption($content['url']['alias'] ?? '', $content['url']['value'] ?? '', true);
        $content['items'] = collect($content['items'] ?? [])->map(function ($item) use ($mobile_router_service, $romote_search_service) {
            $item['cat_id'] = (int)$item['cat_id'];
            $item['default_categories'] = $romote_search_service->getOption(RomoteSearchService::TYPE_APP_CATEGORY, $item['cat_id'], true);
            $item['goods_items'] = collect($item['goods_items'])->map(function ($goods_item) use ($mobile_router_service) {
                $goods_item['default_selection_data'] = $mobile_router_service->getOption(Router::GOODS, $goods_item['goods_id'], true);
                $goods_item['goods_id'] = (int)$goods_item['goods_id'];

                return $goods_item;
            })->sortByDesc('sort')->values()->toArray();

            return $item;
        })->sortByDesc('sort')->values()->toArray();

        return [
            'id' => $data['id'] ?? 0,
            'name' => $data['name'] ?? '',
            'component_name' => $data['component_name'] ?? '',
            'is_show' => $data['is_show'] ?? 1,
            'is_fixed_assembly' => $data['is_fixed_assembly'] ?? 0,
            'sort' => $data['sort'] ?? 0,
            'content' => $content ?? null,
            'data' => $this->getContent($data),
        ];
    }

    /**
     * verify request parameters
     * {@inheritDoc}
     */
    public function validate(array $data): array
    {
        $is_show_validate_string = Constant::ONE.','.Constant::ZERO;
        $mobile_router_service = new MobileRouterService();
        $validator = Validator::make($data, [
            'id' => 'present|nullable|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_HOT_SALE_GOOD,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required|array',
            'content.title_is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content.icon' => 'present|nullable',
            'content.title' => 'present|nullable|max:6|required_if:content.title_is_show,'.Constant::ONE,
            'content.url.alias' => 'present|nullable',
            'content.url.value' => 'present|nullable',
            'content.items' => 'required|array',
            'content.items.*.cat_id' => 'required|exists:\App\Models\Category,cat_id',
            'content.items.*.alias' => 'required|max:10',
            'content.items.*.sort' => 'present|nullable|integer|min:1|max:100',
            'content.items.*.goods_items' => 'required|array',
            'content.items.*.goods_items.*.goods_id' => 'required',
            'content.items.*.goods_items.*.sort' => 'present|nullable|integer|min:1|max:100',
        ], $this->message());

        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();

        if (count($validate['content']['items']) > 10) {
            throw new BusinessException($this->getName().'最多允许添加10个');
        }
        // 校验url链接的正确性
        if ($validate['content']['url']['alias'] && $validate['content']['title_is_show']) {
            try {
                $mobile_router_service->viodData($validate['content']['url']['alias'], $validate['content']['url']['value']);
            } catch (\Exception $exception) {
                throw new BusinessException($this->getName().'url链接,'.$exception->getMessage());
            }
        }
        //分类id、分类别名 不能重复
        $temp_cat_ids = collect($validate['content']['items'])->pluck('cat_id')->toArray();
        if(count($temp_cat_ids) != count(array_unique($temp_cat_ids))){
            throw new BusinessException($this->getName().'推荐分类不能重复');
        }
        $temp_alias = collect($validate['content']['items'])->pluck('alias')->toArray();
        if(count($temp_alias) != count(array_unique($temp_alias))){
            throw new BusinessException($this->getName().'分类别名不能重复');
        }
        //分类别名不能重复
        // 处理排序
        $validate['content']['items'] = collect($validate['content']['items'])->map(function ($item) {
            if (count($item['goods_items']) > 10) {
                throw new BusinessException($this->getName().'推荐商品最多允许添加10个');
            }
            // 处理排序
            $item['sort'] = $item['sort'] ? (int) $item['sort'] : 1;
            $item['goods_items'] = collect($item['goods_items'])->map(function ($goods_item) {
                $goods_item['sort'] = $goods_item['sort'] ? (int) $goods_item['sort'] : 1;
                $goods_item['goods_id'] = (int)$goods_item['goods_id'];
                return $goods_item;
            })->sortByDesc('sort')->values()->toArray();
            $item['cat_id'] = (int)$item['cat_id'];
            return $item;
        })->sortByDesc('sort')->values()->toArray();

        return [
            'id' => $validate['id'],
            'name' => $validate['name'],
            'component_name' => $validate['component_name'],
            'is_show' => $validate['is_show'],
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => (int) ($validate['sort'] ?? 0),
            'content' => $validate['content'],
        ];
    }

    private function message()
    {
        return [
            'id.present' => '板块ID 未设置',
            'id.integer' => '板块ID 格式不正确',
            'id.exists' => '板块ID不存在，请刷新重试',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过:max个字符',
            'component_name.required' => '未设置组件别名，请刷新页面后重试',
            'component_name.in' => '组件别名参数不正确，请刷新页面后重试',
            'is_show.required' => '请设置板块是否展示',
            'is_show.integer' => '板块是否展示参数格式不正确',
            'is_show.in' => '板块是否展示参数格式不正确',
            'content.required' => '请设置板块对应数据',
            'content.array' => 'content数据格式不正确',
            'content.title_is_show.required' => '请设置标题是否显示',
            'content.title_is_show.integer' => '标题是否显示格式不正确',
            'content.title_is_show.in' => '标题是否显示格式不正确',
            'content.icon.present' => '请设置ICON参数',
            'content.title.present' => '请设置标题参数',
            'content.title.max' => '标题不能超过:max个字段',
            'content.title.required_if' => '标题是必填项',
            'content.url.alias.present' => '请设置URL链接参数',
            'content.url.value.present' => '请设置URL链接值参数',
            'content.items.required' => '请设置推荐商品',
            'content.items.array' => '推荐商品格式不正确',
            'content.items.*.cat_id.required' => '请选择推荐分类',
            'content.items.*.cat_id.exists' => '推荐分类不存在',
            'content.items.*.alias.required' => '请输入分类别名',
            'content.items.*.alias.max' => '分类别名不能超:max个字符',
            'content.items.*.sort.present' => '请设置分类排序参数',
            'content.items.*.sort.integer' => '分类排序参数格式不正确',
            'content.items.*.sort.min' => '排序最小值是:min',
            'content.items.*.sort.max' => '排序最大值是:max',
            'content.items.*.goods_items.required' => '请设置关联商品',
            'content.items.*.goods_items.array' => '关联商品参数格式不正确',
            'content.items.*.goods_items.*.goods_id.required' => '请选择商品',
            'content.items.*.goods_items.*.sort.present' => '请设置商品排序参数',
            'content.items.*.goods_items.*.sort.integer' => '商品排序参数格式不正确',
            'content.items.*.goods_items.*.sort.min' => '排序最小值是:min',
            'content.items.*.goods_items.*.sort.max' => '排序最大值是:max',
        ];
    }
}
