<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Router;
use App\Models\User;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use App\Utils\Route;
use Illuminate\Support\Facades\Validator;

/**
 * 频道组件.
 */
class ChannelSquareComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => '频道广场',
            'component_name' => $this->getComponentName(),
            'limit' => -1,
            'icon' => '&#xe7ca;',
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
            'name' => '频道广场',
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO,
            'sort' => Constant::ZERO,
            'content' => [
                'items' => [
                    [
                        'type' => AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM,  //板块类型
                        'title_style' => AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_ICON,  //标题样式
                        'icon' => '',  //icon
                        'title' => '', //标题
                        'label' => '', //标签
                        'label_bg_color' => '#F71111',  //标签背景色
                        'subtitle' => '',  //副标题-自定义板块独有
                        'subtitle_font_color' => '#F71111', //副标题文字颜色-自定义板块独有
                        'url' => [  //url链接
                            'alias' => 'https',  //别名 如：article_detail
                            'value' => '', //alias对应的参数值 || https对应的地址
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => '', //排序
                        'price_index' => [ //价格指数独有
                            'data_source' => AppWebsiteDecorationItem::DATA_SOURCE_PRICE_INDICES,
                            'price_item' => [
                                //						[
                                //							'name' => '',
                                //							'type' => '',
                                //							'product_id' => '',
                                //						],
                            ],
                        ],
                        'goods_up_down_data_type' => AppWebsiteDecorationItem::GOODS_UP_DOWN_DATA_TYPE_DATE,
                        'goods_up_down_num' => '',
                        'image_items' => [ //自定义板块独有
                            [
                                'image' => '',
                                'url' => [
                                    'alias' => 'https',
                                    'value' => '',
                                    'default_selection_data' => [], //选中的数据
                                ],
                                'sort' => '',
                            ],
                            [
                                'image' => '',
                                'url' => [
                                    'alias' => 'https',
                                    'value' => '',
                                    'default_selection_data' => [], //选中的数据
                                ],
                                'sort' => '',
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
        $items = [];
        if ($content['items'] ?? []) {
            $user = $data['user'] ?? null;
            $source = $data['source'] ?? '';
            $mobileRouterService = app(MobileRouterService::class);
            collect($content['items'])->sortByDesc('sort')->map(function ($item,$key) use (&$items, $mobileRouterService, $user, $source) {
                $url = '';
                $url_alias = '';
                switch ($item['type']) {
                    case AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM:
                        $image = [];
                        collect($item['image_items'] ?? [])->sortByDesc('sort')->map(function ($img_item) use (&$image, $mobileRouterService, $source) {
                            if (!(is_harmony_request() && in_array($img_item['url']['alias'],Router::$harmony_no_show))) {
                                if (($img_item['url']['alias'] ?? '')) {
                                    $url = $mobileRouterService->handleUrl($img_item['url']['alias'], $img_item['url']['value'] ?? '', $source);
                                }
                                $image[] = [
                                    'image' => $img_item['image'] ?? '',
                                    'url' => $url ?? '',
                                    'url_alias' => $img_item['url']['alias'] ?? '',
                                ];
                            }

                        });
                        $data = $image;

                        break;
                    default:
                        $data = [];
                }
                if($data){
                    if ($item['url']['alias'] ?? '') {
                        $url = $mobileRouterService->handleUrl($item['url']['alias'], $item['url']['value'] ?? '', $source);
                        $url_alias = $item['url']['alias'];
                    }

                    $result = [
                        'type' => $item['type'],  //板块类型
                        'title_style' => $item['title_style'],  //标题样式
                        'icon' => $item['icon'],  //icon
                        'title' => $item['title'], //标题
                        'label' => $item['label'], //标签
                        'label_bg_color' => $item['label_bg_color'],  //标签背景色
                        'subtitle' => $item['subtitle'],  //副标题-自定义板块独有
                        'subtitle_font_color' => $item['subtitle_font_color'], //副标题文字颜色-自定义板块独有
                        'url' => $url,
                        'url_alias' => $url_alias,
                        'is_login' => $user instanceof User ? true : false,
                    ];
                    if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM){
                        $result['custom_data'] = $data;
                    }elseif($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX){
                        $result['price_data'] = $data;
                    }else{
                        $result['up_down_data'] = $data;
                    }
                    $items[] = $result;
                }

            });
        }

        return [
            'component_name' => $this->getComponentName(),
            'items' => $items,
            'sort' => $data['sort']??0,
        ];
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
        $mobile_router_service = new MobileRouterService();
        $content['items'] = collect($content['items'] ?? [])->map(function ($item) use ($mobile_router_service) {
            $item['url']['default_selection_data'] = $mobile_router_service->getOption($item['url']['alias'] ?? '', $item['url']['value'] ?? '', true);
            if (AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM == $item['type']) {
                $item['image_items'] = collect($item['image_items'])->map(function ($image_item) use ($mobile_router_service) {
                    $image_item['url']['default_selection_data'] = $mobile_router_service->getOption($image_item['url']['alias'] ?? '', $image_item['url']['value'] ?? '', true);

                    return $image_item;
                })->toArray();
            }
            $item['goods_up_down_data_type'] = $item['goods_up_down_data_type']??AppWebsiteDecorationItem::GOODS_UP_DOWN_DATA_TYPE_DATE;
            $item['goods_up_down_num'] = $item['goods_up_down_num']??'';
            return $item;
        })->toArray();

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
            'component_name' => 'required|in:'.AppWebsiteDecorationItem::COMPONENT_NAME_CHANNEL_SQUARE,
            'is_show' => 'required|integer|in:'.$is_show_validate_string,
            'content' => 'required|array',
            'content.items' => 'required|array',
            'content.items.*.type' => 'required|in:'.AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN.','.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX.','.AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM,
            'content.items.*.title_style' => 'required|in:'.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_ICON.','.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_ICON_TITLE.','.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_TITLE_LABEL,
            'content.items.*.icon' => 'present|nullable|required_if:content.items.*.title_style,'.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_ICON_TITLE,
            'content.items.*.title' => 'required|max:6|distinct',
            'content.items.*.label' => 'present|nullable|required_if:content.items.*.title_style,'.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_TITLE_LABEL.'|distinct|max:6', // 数据校验
            'content.items.*.label_bg_color' => 'present|nullable|required_if:content.items.*.title_style,'.AppWebsiteDecorationItem::CHANNEL_TITLE_STYLE_TITLE_LABEL, // 数据校验
            'content.items.*.subtitle' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM.'|max:10', // 数据校验
            'content.items.*.subtitle_font_color' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM, // 数据校验
            'content.items.*.url.alias' => 'present|nullable',
            'content.items.*.url.value' => 'present|nullable',
            'content.items.*.sort' => 'present|nullable|integer|min:1|max:100',
            'content.items.*.price_index' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX,
            'content.items.*.price_index.data_source' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX.'|integer|in:'.AppWebsiteDecorationItem::DATA_SOURCE_PRICE_INDICES.','.AppWebsiteDecorationItem::DATA_SOURCE_INDEX_ITEMS,
            'content.items.*.price_index.price_item' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX.'|array',
            'content.items.*.price_index.price_item.*.name' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX,
            'content.items.*.price_index.price_item.*.type' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX,
            'content.items.*.price_index.price_item.*.product_id' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX,
            'content.items.*.goods_up_down_data_type' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN.'|integer|in:'.AppWebsiteDecorationItem::GOODS_UP_DOWN_DATA_TYPE_DATE.','.AppWebsiteDecorationItem::GOODS_UP_DOWN_DATE_NUM_TYPE,
            'content.items.*.goods_up_down_num' => 'present|nullable|required_if:content.items.*.goods_up_down_data_type,'.AppWebsiteDecorationItem::GOODS_UP_DOWN_DATE_NUM_TYPE.'|integer',
            'content.items.*.image_items' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM.'|array|size:2',
            'content.items.*.image_items.*.image' => 'present|nullable|required_if:content.items.*.type,'.AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM,
            'content.items.*.image_items.*.url.alias' => 'present|nullable',
            'content.items.*.image_items.*.url.value' => 'present|nullable',
            'content.items.*.image_items.*.sort' => 'present|nullable|integer|min:1|max:100',
        ], $this->message());

        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();
        //唯一性
        $up_down = collect($validate['content']['items'])->where('type',AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN)->count();
        $price_index = collect($validate['content']['items'])->where('type',AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX)->count();
        if($up_down>1){
            throw new BusinessException($this->getName().'商品涨跌,具备全局唯一特性，无法设置多次');
        }
        if($price_index>1){
            throw new BusinessException($this->getName().'价格指数,具备全局唯一特性，无法设置多次');
        }

        if (count($validate['content']['items']) > 10) {
            throw new BusinessException($this->getName().'最多允许添加10个');
        }

        //处理排序
        $validate['content']['items'] = collect($validate['content']['items'])->map(function ($item,$key)use($mobile_router_service) {
            if (count($item['price_index']['price_item'] ?? []) > 10) {
                throw new BusinessException($this->getName().'价格指数推荐产品最多允许添加10个');
            }
            //校验url链接的正确性
            if (in_array($item['type'], [AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN, AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX])) {
                try {
                    $mobile_router_service->viodData($item['url']['alias'] ?? '', $item['url']['value'] ?? '');
                } catch (\Exception $exception) {
                    throw new BusinessException($this->getName().'：第'.($key + 1).'个菜单下，'.$exception->getMessage());
                }
            } else {
                collect($item['image_items'])->map(function ($image_item, $image_key) use ($mobile_router_service, $key) {
                    try {
                        $mobile_router_service->viodData($image_item['url']['alias'] ?? '', $image_item['url']['value'] ?? '');
                    } catch (\Exception $exception) {
                        throw new BusinessException($this->getName().'：第'.($key + 1).'个菜单下，第'.($image_key + 1).'张图片，'.$exception->getMessage());
                    }
                });
            }
            //处理排序
            $item['sort'] = $item['sort'] ? (int) $item['sort'] : 1;
            //处理默认值
            if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN && $item['goods_up_down_data_type'] == AppWebsiteDecorationItem::GOODS_UP_DOWN_DATA_TYPE_DATE){
                $item['goods_up_down_num'] = '';
            }
            if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN && $item['goods_up_down_data_type'] == AppWebsiteDecorationItem::GOODS_UP_DOWN_DATE_NUM_TYPE){
                if($item['goods_up_down_num']<1 || $item['goods_up_down_num']>100){
                    throw new BusinessException($this->getName().'：第'.($key + 1).'个菜单下,展示数量的填写范围是1~100');
                }
            }
            if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN || $item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM){
                $item['price_index'] = [
                    'data_source' => AppWebsiteDecorationItem::DATA_SOURCE_PRICE_INDICES,
                    'price_item' => [],
                ];
            }
            if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_UP_DOWN || $item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_PRICE_INDEX){
                $item['subtitle'] = '';
                $item['subtitle_font_color'] = '';
                $item['image_items'] = [ //自定义板块独有
                    [
                        'image' => '',
                        'url' => [
                            'alias' => 'https',
                            'value' => '',
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => '',
                    ],
                    [
                        'image' => '',
                        'url' => [
                            'alias' => 'https',
                            'value' => '',
                            'default_selection_data' => [], //选中的数据
                        ],
                        'sort' => '',
                    ],
                ];
            }
            if($item['type'] == AppWebsiteDecorationItem::CHANNEL_TYPE_CUSTOM){
                $item['image_items'] = collect($item['image_items'])->map(function ($img_item) {
                    $img_item['sort'] = $img_item['sort'] ? (int) $img_item['sort'] : 1;

                    return $img_item;
                })->sortByDesc('sort')->values()->toArray();
            }
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
            'content.items.required' => '请设置板块对应数据',
            'content.items.array' => '板块数据格式不正确',
            'content.items.*.type.required' => '板块类型未设置',
            'content.items.*.type.in' => '板块类型参数不正确，请刷新页面后重试',
            'content.items.*.title_style.required' => '板块标题样式未设置',
            'content.items.*.title_style.in' => '标题样式参数不正确，请刷新页面后重试',
            'content.items.*.icon.present' => 'icon参数未设置',
            'content.items.*.icon.required_if' => '请上传图片',
            'content.items.*.title.required' => '请输入标题',
            'content.items.*.title.max' => '标题不能超过:max个字符',
            'content.items.*.title.distinct' => '该标题已存在，请修改！',
            'content.items.*.label.present' => '标签参数未设置',
            'content.items.*.label.required_if' => '标签未设置',
            'content.items.*.label.distinct' => '该标签已存在，请修改！',
            'content.items.*.label.max' => '标签不能超过:max个字符',
            'content.items.*.label_bg_color.present' => '标签背景颜色参数未设置',
            'content.items.*.label_bg_color.required_if' => '标签背景颜色未设置',
            'content.items.*.subtitle.present' => '副标题参数未设置',
            'content.items.*.subtitle.required_if' => '副标题未设置',
            'content.items.*.subtitle.max' => '副标题不能超过:max个字符',
            'content.items.*.subtitle_font_color.present' => '副标题文字杨色未设置',
            'content.items.*.subtitle_font_color.required_if' => '副标题文字杨色未设置',
            'content.items.*.url.alias.present' => 'url链接别名参数未设置',
            'content.items.*.url.value.present' => 'url链接值未设置参数',
            'content.items.*.sort.present' => '排序参数未设置',
            'content.items.*.sort.integer' => '排序参数格式不正确',
            'content.items.*.sort.max' => '排序最大值是:max',
            'content.items.*.sort.min' => '排序最小值是:min',
            'content.items.*.price_index.present' => '请设置价格指数推荐产品参数',
            'content.items.*.price_index.required_if' => '请设置价格指数推荐产品数据',
            'content.items.*.price_index.data_source.present' => '数据来源参数未设置',
            'content.items.*.price_index.data_source.required_if' => '数据来源未设置',
            'content.items.*.price_index.data_source.integer' => '数据来源参数格式未不正确',
            'content.items.*.price_index.data_source.in' => '数据来源参数不正确，请刷新页面后重试',
            'content.items.*.price_index.price_item.present' => '价格指数产品参数未设置',
            'content.items.*.price_index.price_item.required_if' => '价格指数产品未设置',
            'content.items.*.price_index.price_item.array' => '价格指数产品数据格式不正确',
            'content.items.*.price_index.price_item.*.name.present' => '价格指数产品参数name未设置',
            'content.items.*.price_index.price_item.*.name.required_if' => '价格指数产品参数未设置',
            'content.items.*.price_index.price_item.*.type.present' => '价格指数产品参数type未设置',
            'content.items.*.price_index.price_item.*.type.required_if' => '价格指数产品参数未设置',
            'content.items.*.price_index.price_item.*.product_id.present' => '价格指数产品参数product_id未设置',
            'content.items.*.price_index.price_item.*.product_id.required_if' => '价格指数产品参数未设置',
            'content.items.*.goods_up_down_data_type.present' => '商品涨跌数据规则未设置',
            'content.items.*.goods_up_down_data_type.required_if' => '商品涨跌数据规则未设置',
            'content.items.*.goods_up_down_data_type.integer' => '商品涨跌数据规则格式不正确',
            'content.items.*.goods_up_down_data_type.in' => '商品涨跌数据规则格参数不正确',
            'content.items.*.goods_up_down_num.present' => '商品涨跌展示数量未设置',
            'content.items.*.goods_up_down_num.required_if' => '商品涨跌展示数量未设置',
            'content.items.*.goods_up_down_num.integer' => '商品涨跌展示数量格式不正确',
            'content.items.*.image_items.present' => '请设置自定义板块图片参数',
            'content.items.*.image_items.required_if' => '请设置自定义板块图片数据',
            'content.items.*.image_items.array' => '自定义板块图片数据格式不正确',
            'content.items.*.image_items.size' => '自定义板块图片个数不正确',
            'content.items.*.image_items.*.image.present' => '自定义板块图片参数未设置',
            'content.items.*.image_items.*.image.required_if' => '自定义板块图片未设置',
            'content.items.*.image_items.*.url.alias.present' => '自定义板块图片url链接别名参数未设置',
            'content.items.*.image_items.*.url.value.present' => '自定义板块图片url链接未设置值参数',
            'content.items.*.image_items.*.sort.present' => '自定义板块图片排序参数未设置',
            'content.items.*.image_items.*.sort.integer' => '自定义板块图片排序参数格式不正确',
            'content.items.*.image_items.*.sort.max' => '自定义板块图片排序最大值是:max',
            'content.items.*.image_items.*.sort.min' => '自定义板块图片排序最小值是:min',
        ];
    }
}
