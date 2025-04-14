<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\ProcessDataException;
use App\Models\AppDecorationItem;
use App\Services\Goods\GoodsService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class GoodsRecommendComponent extends PageComponent
{
    public function icon(): array
    {
        return [
            'name' => $this->getName() ?: '商品推荐',
            'component_name' => $this->getComponentName(),
            'limit' => 0,
            'icon' => '&#xe7ce;',
            'sort' => '',
        ];
    }

    public function parameter(): array
    {
        return [
            'id' => '', // 组件自增id
            'name' => $this->getName() ?: '商品推荐',
            'component_name' => $this->getComponentName(), // 组件名称
            'is_show' => Constant::ONE,
            'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
            'sort' => Constant::ZERO,
            'content' => [
                'layout' => AppDecorationItem::LAYOUT_ONE, // 商品布局 1、单列 2、双列 3、三列
                'recommend' => 0, // 智能推荐 1、开启 0、关闭 - 暂不处理
                'title' => [
                    'icon' => '', // 小图标
                    'name' => '', // 标题
                    'align' => 'left', // 对齐方式 left center
                    'suffix' => '', // 标题右侧文案
                    'url' => [
                        'name' => '',
                        'value' => '',
                    ],
                ], // 图片设置
                'goods' => [
                    'rule' => 1, // 推荐规则 1、智能推荐 2、手动推荐
                    'sort_type' => 1, // 排序类型 1、销量 2、好评 3、低价 4、新品
                    'number' => 3, // 数量限制 1 ~ 20
                    'goods_nos' => [],
                ], // 商品设置
            ],
        ];
    }

    /**
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     *
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        $validator = Validator::make($data, [
            'id' => 'nullable',
            'name' => 'required|max:100',
            'component_name' => 'required|in:'.AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND,
            'is_show' => 'required|integer|in:'.Constant::ONE.','.Constant::ZERO,
            'content.layout' => 'required|in:'.AppDecorationItem::LAYOUT_ONE.','.AppDecorationItem::LAYOUT_TWO.','.AppDecorationItem::LAYOUT_THREE, // 商品布局
            'content.recommend' => 'required|in:'.Constant::ONE.','.Constant::ZERO, // 智能推荐 1、开启 0、关闭
            'content.title' => 'required|array',
            'content.title.icon' => 'present|nullable',
            'content.title.name' => 'present|nullable',
            'content.title.align' => 'required|in:'.AppDecorationItem::ALIGN_LEFT.','.AppDecorationItem::ALIGN_CENTER,
            'content.title.suffix' => 'present|nullable',
            'content.title.url.name' => 'present|nullable',
            'content.title.url.value' => 'present|nullable',
            'content.goods' => 'required|array',
            'content.goods.rule' => 'required|in:'.AppDecorationItem::RULE_INTELLIGENT.','.AppDecorationItem::RULE_MANUAL,
            'content.goods.sort_type' => 'required_if:content.goods.rule,1|in:'.AppDecorationItem::SORT_SALES.','.AppDecorationItem::SORT_HIGH_PRAISE.','.AppDecorationItem::SORT_LOW_PRICE.','.AppDecorationItem::SORT_NEW_PRODUCT,
            'content.goods.number' => 'required_if:content.goods.rule,1|int|min:1|max:20',
            'content.goods.goods_nos' => 'required_if:content.goods.rule,2|array',
        ], $this->messages());

        if ($validator->fails()) {
            throw new ProcessDataException($this->getName().'：'.$validator->errors()->first(), ['id' => $data['id']]);
        }
        $data = $validator->validated();
        $data['name'] = '商品推荐';
        $data['is_fixed_assembly'] = Constant::ZERO;
        $data['component_name'] = AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND;

        return $data;
    }

    /**
     * 格式化组件数据.
     */
    public function getContent($data): array
    {
        $content = $data['content'];
        $goodsService = new GoodsService;
        $items = collect($content['goods'])
//            ->sortByDesc('sort')
            ->map(function ($item) use (&$items, $goodsService) {
                $data['rule'] = $item['rule'];
                $data['number'] = $item['number'] ?? Constant::ZERO;
                $data['sort_type'] = $item['sort_type'] ?? Constant::ZERO;
                $data['goods_nos'] = $item['goods_nos'] ?? null;
                $data['goods_data'] = $goodsService->getRecommendGoods($data['number'], $data['sort_type'], $data['goods_nos'], $data['rule']);

                return $data;
            })->filter()->values()->toArray();

        return [
            'component_name' => $data['component_name'],
            'sort' => $data['sort'] ?? 0,
            'layout' => $content['layout'],
            'recommend' => $content['recommend'],
            'title' => $content['title'],
            'items' => $items,
        ];
    }

    /**
     * 后台数据渲染.
     */
    public function display($data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'sort' => $data['sort'],
            'content' => $data['content'] ?? null, // 表单提交数据
            'data' => $this->getContent($data), // 战术数据
        ];
    }

    private function messages(): array
    {
        return [
            'id.integer' => '板块ID 必须是整数',
            'id.exists' => '板块ID 不存在',
            'name.required' => '请设置板块名称',
            'name.max' => '板块名称不能超过 100 个字符',
            'component_name.required' => '组件名称不能为空',
            'component_name.in' => '组件名称无效，请选择正确的组件名称',
            'is_show.required' => '是否显示不能为空',
            'is_show.integer' => '是否显示必须是整数',
            'is_show.in' => '是否显示的值只能是 0 或 1',
            'content.required' => '内容数据不能为空',
            'content.array' => '内容数据必须是一个数组',
            'content.layout.required' => '商品布局不能为空',
            'content.layout.in' => '商品布局的值无效，请选择正确的布局类型',
            'content.recommend.required' => '智能推荐设置不能为空',
            'content.recommend.in' => '智能推荐的值无效，请选择正确的设置',
            'content.title.required' => '标题数据不能为空',
            'content.title.array' => '标题数据必须是一个数组',
            'content.title.icon.present' => '小图标字段不能为空',
            'content.title.name.present' => '标题名称参数未设置',
            'content.title.align.required' => '对齐方式不能为空',
            'content.title.align.in' => '对齐方式的值无效，请选择正确的对齐方式',
            'content.title.suffix.present' => '标题右侧文案参数未设置',
            'content.title.url.name.present' => '标题链接别名参数未设置',
            'content.title.url.value.present' => '标题链接值参数未设置',
            'content.goods.required' => '商品设置不能为空',
            'content.goods.array' => '商品设置必须是一个数组',
            'content.goods.rule.required' => '推荐规则不能为空',
            'content.goods.rule.in' => '推荐规则的值无效，请选择正确的推荐规则',
            'content.goods.sort_type.required_if' => '排序类型不能为空（当推荐规则为智能推荐时）',
            'content.goods.sort_type.in' => '排序类型的值无效，请选择正确的排序类型',
            'content.goods.number.required_if' => '数量限制不能为空（当推荐规则为智能推荐时）',
            'content.goods.number.int' => '数量限制必须为整数',
            'content.goods.number.min' => '数量限制最小值是 1',
            'content.goods.number.max' => '数量限制最大值是 20',
            'content.goods.goods_nos.required_if' => '商品编号列表不能为空（当推荐规则为手动推荐时）',
            'content.goods.goods_nos.array' => '商品编号列表必须是一个数组',
        ];
    }
}
