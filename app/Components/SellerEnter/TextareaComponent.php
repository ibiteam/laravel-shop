<?php

namespace App\Components\SellerEnter;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Validator;

class TextareaComponent extends PageComponent
{
    private string $icon = '&#xe82f;';

    public function icon(): array
    {
        return [
            'name' => '多行文本',
            'type' => $this->getComponentName(),
            'limit' => 0,
            'icon' => $this->icon,
            'sort' => '',
        ];
    }

    public function parameter(): array
    {
        return [
            'name' => '',
            'type' => $this->getComponentName(),
            'is_need' => 1,
            'is_show' => 1,
            'tips' => '',
            'limit_type' => '',
            'limit_number' => 1,
            'select_options' => [],
            'template_name' => '',
            'template_url' => '',
            'style_type' => '',
            'style' => '',
            'sort' => '',
        ];
    }

    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'id' => 'present|nullable|exists:\App\Models\SellerEnterConfig,id',
            'name' => 'required',
            'type' => 'required|in:'.$this->getComponentName(),
            'is_need' => 'required|boolean', // 是否必填
            'is_show' => 'required|in:1', // 是否显示
            'tips' => 'present|nullable|string|max:30', // 提示文字
        ], [
            'id.present' => '板块ID 未设置',
            'id.exists' => '板块ID不存在，请刷新重试',
            'name.required' => '请设置板块名称',
            'type.required' => '板块类型 未设置',
            'type.in' => '板块类型 格式不正确',
            'is_need.required' => '板块是否必填 未设置',
            'is_need.boolean' => '板块是否必填 格式不正确',
            'is_show.required' => '板块是否显示 未设置',
            'is_show.in' => '板块是否显示 格式不正确',
            'tips.present' => '板块提示文字 未设置',
            'tips.nullable' => '板块提示文字 未设置',
            'tips.string' => '板块提示文字 格式不正确',
            'tips.max' => '板块提示文字 不能超过 :max 个字符',
        ]);
        if ($validator->fails()) {
            throw new BusinessException($this->getName().$validator->errors()->first());
        }
        $validator->excludeUnvalidatedArrayKeys = true;
        $validate = $validator->validated();

        return [
            'id' => $validate['id'],
            'name' => $validate['name'],
            'type' => $validate['type'],
            'is_need' => $validate['is_need'],
            'limit_number' => 1,
            'is_show' => 1,
            'tips' => $validate['tips'],
        ];
    }

    public function getContent(array $data): array
    {
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'is_need' => $data['is_need'],
            'limit_type' => $data['limit_type'],
            'limit_number' => $data['limit_number'],
            'is_show' => $data['is_show'],
            'select_options' => $data['select_options'],
            'template_name' => $data['template_name'],
            'template_url' => $data['template_url'],
            'tips' => $data['tips'],
            'style_type' => $data['style_type'],
            'style' => $data['style'],
            'sort' => $data['sort'],
        ];
    }

    public function display(array $data): array
    {
        if (empty($data)) {
            return $this->display($this->parameter());
        }
        $res = $this->getContent($data);
        $res['icon'] = $this->icon;

        return $res;
    }
}
