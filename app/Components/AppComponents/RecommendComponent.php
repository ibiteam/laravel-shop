<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\ViewsDao;
use App\Models\AppWebsiteDecorationItem;
use App\Models\Category;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class RecommendComponent extends PageComponent
{

    const TYPE_GOODS = 1;
    const TYPE_AD = 2;

    const RECOMMEND_CENTER = 'recommend_center';
    const RECOMMEND_CAT = 'recommend_cat';
    public function icon(): array
    {
        return [
            'name' => '为您推荐',
            'component_name' => $this->getComponentName(),
            'limit' => 1,
            'icon' => "&#xe7d7",
            'sort' => "",
        ];
    }

    public function parameter(): array
    {

        switch ($this->getComponentName()){
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME:
                $params = [
                    'type' => AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME,
                    'name' => '',
                    'theme' => [
                        [
                            'title' => '',
                            'subtitle' => '',
                            'cat_id' => 0,
                            'cat_name' => '',
                            'sort' => 1
                        ],
                    ],
                    'number' => 20,
                    'is_show_ziying' => 0,
                    "is_show_ad" => 0,
                    'ad_space' => [
                        [

                            'ad_name' => '',
                            'space' => 5,
                            'alias' => 'https',
                            'value' => '',
                            'sort' => 1,
                            'img_data' => [
                                [
                                    'img_url' => '',
                                    'alias' => 'https',
                                    'value' => '',
                                    'sort' =>1
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT:
                $params = [
                    'type' => AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT,
                    'name' => '',
                    'number' => 20,
                    'is_show_ziying' => 0
                ];
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP:
                $params = [
                    'type' => AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP,
                    'name' => '',
                    'number' => 20,
                    'is_show_ziying' => 0
                ];
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER:
                $params = [
                    'type' => self::RECOMMEND_CENTER,
                    'name' => '',
                    'number' => 20,
                    'is_show_ziying' => 0,
                    'cat' => [
                        [
                            'cat_id' => 0,
                            'cat_name' => '',
                            "cat_alars"=>"",
                            "sort"=>1,
                            "number"=>20,
                            "is_show_ziying"=>0,
                        ]
                    ]
                ];
                break;
            default:
                $params = [];
        }

        return [
            'name' => "为您推荐",
            'id' => 0,
            'component_name' => $this->getComponentName(),
            'is_fixed_assembly' => Constant::ZERO,
            'is_show' => Constant::ONE,
            'content' => $params,
        ];
    }

    /**
     * @param $data
     * @return array|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * @throws \App\Exceptions\BusinessException
     */
    public function validate($data): array
    {
        if(in_array($this->getComponentName(),[AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP])){
            $recommend_type = $this->getComponentName();
        }else{
            $recommend_type = $data['content']['type'];
        }
        switch ($recommend_type){
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME:
                if(!$data['content']['theme']){
                    $data['content']['theme'] = '';
                }
                $validator = Validator::make($data, [
                    'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
                    'name' => 'required',
                    'is_show' => 'required',
                    'content.name' => 'nullable',
                    'content.number' => 'required|integer',
                    'content.is_show_ad' => 'required|integer',
                    'content.is_show_ziying' => 'required|integer',
                    'content.theme' => 'nullable|array|min:3|max:4',
                    'content.theme.*.title' => 'required',
                    'content.theme.*.subtitle' => 'required',
                    'content.theme.*.cat_id' => 'required',
                    'content.theme.*.cat_name' => 'required',
                    'content.theme.*.sort' => 'nullable|min:1|max:100',
                    'content.ad_space' => 'required_if:content.is_show_ad,1|nullable|array|max:10',
                    'content.ad_space.*.ad_name' => 'required_if:content.is_show_ad,1',
                    'content.ad_space.*.space' => 'required_if:content.is_show_ad,1|integer|min:1|max:60',
                    'content.ad_space.*.img_data' => 'required_if:content.is_show_ad,1|array|max:5',
                    'content.ad_space.*.img_data.*.img_url' => 'required_if:content.is_show_ad,1',
                    'content.ad_space.*.img_data.*.alias' => 'nullable',
                    'content.ad_space.*.img_data.*.value' => 'nullable',
                    'content.ad_space.*.img_data.*.sort' => 'nullable|min:1|max:100',

                ], [
                    'name.required' => '模块名称不能为空',
                    'is_show.required' => '模块是否显示',
//                    'content.name.nullable' => '请设置推荐名称',
                    'content.number.required' => '请选择推荐每页推荐数',
                    'content.is_show_ziying.required' => '请选择是否显示自营标签',
                    'content.is_show_ad.required' => '请选择是否开启中插广告',
                    'content.theme.min' => '主题最少添加3个',
                    'content.theme.max' => '主题最多添加4个',
                    'content.theme.*.title.required' => '请设置主标题',
                    'content.theme.*.subtitle.required' => '请设置副标题',
                    'content.theme.*.cat_id.required' => '请选择分类(id)',
                    'content.theme.*.cat_name.required' => '请选择分类(名称)',
                    'content.theme.*.sort.min' => '排序最小值为1',
                    'content.theme.*.sort.max' => '排序最大值为100',
                    'content.ad_space.max' => '广告位最多添加10个',
                    'content.ad_space.*.ad_name.required_if' => '请设置广告位名称',
                    'content.ad_space.*.space.required_if' => '请设置广告位名称',
                    'content.ad_space.*.space.max' => '广告位位置最大60',
                    'content.ad_space.*.space.min' => '广告位位置最小1',
                    'content.ad_space.*.img_data.required_if' => '广告图片不能为空',
                    'content.ad_space.*.img_data.max' => '广告位最多设置10个',
                    'content.ad_space.*.img_data.*.img_url.required_if' => '请上传广告位图片',
//                    'content.ad_space.*.img_data.*.alias.nullable' => '请设置广告连接',
//                    'content.ad_space.*.img_data.*.value.nullable' => '请设置广告连接',
                    'content.ad_space.*.img_data.*.sort.min' => '排序最小值为1',
                    'content.ad_space.*.img_data.*.sort.max' => '排序最大值为100',
                ]);

                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT:
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP:
                $validator = Validator::make($data, [
                    'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
                    'name' => 'required',
                    'is_show' => 'required',
                    'content.name' => 'nullable',
                    'content.number' => 'required|integer',
                    'content.is_show_ziying' => 'required|integer',
                ], [
                    'name.required' => '模块名称不能为空',
                    'is_show.required' => '模块是否显示',
//                    'content.name.required' => '请设置推荐名称',
                    'content.is_show_ziying' => '请选择是否展示自营标签',
                    'content.number.required' => '请选择推荐每页推荐数',
                ]);
                break;
            case self::RECOMMEND_CENTER:
                $validator = Validator::make($data, [
                    'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
                    'name' => 'required',
                    'is_show' => 'required',
                    'content.name' => 'nullable',
                    'content.type' => 'required',
                    'content.number' => 'required|integer',
                    'content.is_show_ziying' => 'required|integer',
                    'content.cat' => 'required|array|min:1|max:10',
                    'content.cat.*.cat_name' => 'required',
                    'content.cat.*.cat_id' => 'required',

                ], [
                    'name.required' => '模块名称不能为空',
                    'is_show.required' => '模块是否显示',
                    'content.type' => '推荐类型不能为空',
//                    'content.name.required' => '请设置推荐名称',
                    'content.number.required' => '请选择推荐每页推荐数',
                    'content.is_show_ziying.required' => '请选择是否显示自营标签',
                    'content.cat.required' => '请推荐并添加分类',
                    'content.cat.array' => '分类数据格式不正确',
                    'content.cat.min' => '请选择推荐分类',
                    'content.cat.max' => '推荐分类最多选择10个',
                    'content.cat.*.cat_name.required' => '分类名称不能为空',
                    'content.cat.*.cat_id.required' => '分类id不能为空',
                ]);
                break;
            case self::RECOMMEND_CAT:
                $validator = Validator::make($data, [
                    'id' => 'nullable|integer|exists:\App\Models\AppWebsiteDecorationItem,id',
                    'name' => 'nullable',
                    'is_show' => 'required',
                    'content.cat' => 'required|array|min:1|max:10',
                    'content.type' => 'required',
                    'content.cat.*.cat_name' => 'required',
                    'content.cat.*.cat_id' => 'required',
                    'content.cat.*.cat_alars' => 'required',
                    'content.cat.*.is_show_ziying' => 'required',
                    'content.cat.*.number' => 'required',
                    'content.cat.*.sort' => 'nullable|min:1|max:100',

                ], [
//                    'name.nullable' => '模块名称不能为空',
                    'is_show.required' => '模块是否显示',
                    'content.cat.required' => '请推荐并添加分类',
                    'content.cat.array' => '分类数据格式不正确',
                    'content.cat.min' => '请选择推荐分类',
                    'content.type' => '推荐类型不能为空',
                    'content.cat.max' => '推荐分类最多选择10个',
                    'content.cat.*.cat_name.required' => '分类名称不能为空',
                    'content.cat.*.cat_id.required' => '分类id不能为空',
                    'content.cat.*.cat_alars.required' => '请设置分类别名',
                    'content.cat.*.is_show_ziying.required' => '请选择是否显示自营标签',
                    'content.cat.*.number.required' => '请设置显示商品数',
                    'content.cat.*.sort.min' => '排序最小值为1',
                    'content.cat.*.sort.max' => '排序最大值为100',
                ]);
                break;
            default:
                $validator = [];
        }



        if ($validator->fails()) {
            throw new BusinessException($this->getName() . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
        if(isset($data['content']['theme']) && !$data['content']['theme']){
            $data['content']['theme'] = [];
        }
        $data ['component_name'] = $this->getComponentName();
        $data ['is_fixed_assembly'] = Constant::ZERO;
        return $data;
    }

    /**
     * 格式化组件数据
     * @param $data
     * @return array
     */
    public function getContent($data): array
    {
        $recommend_info = $data['content'];
        $user_id = isset($data['user'])? $data['user']['user_id']:0;
        if(in_array($this->getComponentName(),[AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT,AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP])){
            $recommend_type = $this->getComponentName();
        }else{
            $recommend_type = $data['content']['type'];
        }
        $page = request()->input('page',1);
        if(!is_numeric($page)){
            $page = 1;
        }
        switch ($recommend_type){
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME:
                $info['data'] = app(ViewsDao::class)->recommend_theme($recommend_info,$user_id, request()->input('cat_id'),$page);
                $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME;
                $info['name'] = $recommend_info['name'];
                $info['sort'] = $data['sort']??0;
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT:
                $info['data'] = app(ViewsDao::class)->recommend_left($recommend_info,$user_id,10);
                $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_LEFT;
                $info['name'] = $recommend_info['name'];
                $info['sort'] = $data['sort']??0;
                break;
            case AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_SHOP:
                $info['data'] = [];
                break;
            case self::RECOMMEND_CENTER:
                $info['data'] = app(ViewsDao::class)->recommend_center($recommend_info,$page);
                $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER;
                $info['name'] = $recommend_info['name'];
                $info['type'] = self::RECOMMEND_CENTER;
                $info['sort'] = $data['sort']??0;
                break;
            case self::RECOMMEND_CAT:
                $info['data'] = app(ViewsDao::class)->recommend_cat($recommend_info,request()->input('cat_id'),$page);
                $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER;
                $info['type'] = self::RECOMMEND_CAT;
                $info['sort'] = $data['sort']??0;
                break;
            default:
                $info['data'] = [];
        }
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
        if($data['component_name'] == AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_THEME){
            if(isset($content['ad_space'])){
                foreach ($content['ad_space'] as &$val){
                    if(isset($content['img_data'])){
                        foreach ($val['img_data'] as &$v){
                            $v['url'] = app(MobileRouterService::class)->handleUrl($v['alias'],$v['value'],sourcePort());
                        }
                    }
                }
            }
            foreach ($content['theme'] as &$val){
                $val['cat_name'] = Category::whereCatId($val['cat_id'])->value('cat_name');
                $val['default_selection_data'] = [
                    [
                        'value'=> $val['cat_id'],
                        'label' => '【'.$val['cat_id'].'】'.$val['cat_name']
                    ]
                ];
            }
        }elseif ($data['component_name'] == AppWebsiteDecorationItem::COMPONENT_NAME_RECOMMEND_CAT_OR_CENTER && in_array($content['type'],[self::RECOMMEND_CAT,self::RECOMMEND_CENTER])){
            foreach ($content['cat'] as &$value){
                $value['cat_name'] = Category::whereCatId($value['cat_id'])->value('cat_name');
            }
        }
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'component_name' => $data['component_name'],
            'is_show' => $data['is_show'],
            'is_fixed_assembly' => $data['is_fixed_assembly'],
            'content' => $content,
            'data' => $this->getContent($data),
        ];
    }
}
