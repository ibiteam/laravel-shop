<?php

namespace App\Components\AppComponents;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use App\Http\Daos\ViewsDao;
use App\Models\AppWebsiteDecorationItem;
use App\Utils\Constant;
use Illuminate\Support\Facades\Validator;

class BuyAndSellComponent extends PageComponent
{

    public function icon(): array
    {
        return [
            'name' => '常买常逛',
            'component_name' => $this->getComponentName(),
            'limit' => 1,
            'icon' => "&#xe7cb",
            'sort' => "",
        ];
    }

    public function parameter(): array
    {
        return [
            'name' => '常买常逛',
            'id' => 0,//组件自增id
            'component_name' => $this->getComponentName(),
            'is_show' => Constant::ONE,
            'content' => [
                "name"=>"常买常卖"
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
        ], [
            'name.required' => '模块名称不能为空',
            'is_show.required' => '模块是否显示',
            'content.name.required' => '请设置板块名称名称',
            'content.name.max' => '最多10个字符',
        ]);

        if ($validator->fails()) {
            throw new BusinessException($this->getName() . $validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $data = $validator->validated();
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
        $info['items'] = app(ViewsDao::class)->buy_and_sell($data);
        $info['name'] = $data['content']['name'];
        $info['sort'] = $data['sort']??0;
        $info['component_name'] = AppWebsiteDecorationItem::COMPONENT_NAME_BUY_AND_SELL;
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
