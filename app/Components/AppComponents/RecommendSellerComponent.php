<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\SellerShopinfoDao;
use App\Models\AppWebsiteDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class RecommendSellerComponent extends PageComponent
{

    public function icon(): array
    {
        return [
            'name' => '推荐商家',
            'component_name' => $this->getComponentName(),
            'limit' => 1,
            'icon' => "&#xe7d6",
            'sort' => "",
        ];
    }

    public function parameter(): array
    {
        return [
            'name' => '推荐商家',
            'id' => 0,//组件自增id
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'content' => [
                "name" => $this->getName(),
                "alias" => "https",
                "value" => "",
                "cat" => [
                     [
                        "cat_name" => "",
                        "sort" => 0,
                        "shop" => [
                            [
                                "seller_id" => '',
                                "shop_name" => "",
                                "sort" => 1
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param $data
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        $validator = Validator::make($data, [
            'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
            'name' => 'required',
            'is_show' => 'required',
            'content.name' => 'required|max:10',
            'content.alias' => 'nullable',
            'content.value' => 'nullable',
            'content.cat' => 'required|array|min:1|max:10',
            'content.cat.*.cat_name' => 'required|max:5',
            'content.cat.*.sort' => 'required|min:1|max:100',
            'content.cat.*.shop' => 'required|array|min:3|max:10',
            'content.cat.*.shop.*.seller_id' => 'required',
            'content.cat.*.shop.*.shop_name' => 'required',
            'content.cat.*.shop.*.sort' => 'nullable|min:1|max:100',
        ], [
            'name.required' => '模块名称不能为空',
            'is_show.required' => '模块是否显示',
            'sort.required' => '模块排序',
            'content.name.required' => '请设置板块名称',
            'content.name.max' => '最多10个字符',
            'content.cat.required' => '请设置推荐分类',
            'content.cat.min' => '最少设置1个分类',
            'content.cat.max' => '最多设置10个分类',
            'content.cat.*.cat_name.required' => '请设置分类名称',
            'content.cat.*.cat_name.max' => '分类名称最多5个字符',
            'content.cat.*.sort.required' => '请设置排序',
            'content.cat.*.sort.min' => '排序数字在1~100之间',
            'content.cat.*.sort.max' => '排序数字在1~100之间',
            'content.cat.*.shop.required' => '请设置推荐店铺',
            'content.cat.*.shop.min' => '每个分类推荐店铺数量在3~10之间',
            'content.cat.*.shop.max' => '每个分类推荐店铺数量在3~10之间',
            'content.cat.*.shop.*.seller_id.required' => '请选择店铺',
            'content.cat.*.shop.*.shop_name.required' => '请选择店铺',
            'content.cat.*.shop.*.sort.required' => '请设置店铺排序',
            'content.cat.*.shop.*.sort.min' => '店铺排序在1~100之间',
            'content.cat.*.shop.*.sort.max' => '店铺排序在1~100之间',

        ]);

        if ($validator->fails()) {
            throw new BusinessException($this->getName() . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        $data ['component_name'] = $this->getComponentName();
        $data ['is_fixed_assembly'] = Constant::ONE;
        return $data;
    }

    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $info = [];
        $info['data'] = app(SellerShopinfoDao::class)->recommend_seller($data['content']);
        $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SELLER;
        $info['name'] = $data['content']['name'];
        $info['sort'] = $data['sort']??0;
        return $info;
    }

    /**
     * 商家后台数据渲染
     * @param $data
     * @return array
     */
    public function display($data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $content = $data['content'] ?? [];
        foreach ($content['cat'] as &$val){
            foreach ($val['shop'] as &$v){
                $v['default_selection_data'] = [
                    [
                        'value'=> $v['seller_id'],
                        'label' => '【'.$v['seller_id'].'】'.$v['shop_name']
                    ]
                ];
            }
        }
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'sort' => $data['sort'],
            'content' => $content,
            'data' => $this->getContent($data),
        ];
    }
}
