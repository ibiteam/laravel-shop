<?php

namespace App\Components\SellerEnter;

use App\Components\PageComponent;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Validator;

class InputComponent extends PageComponent
{
    private string $limit_type_phone = 'phone';
    private string $limit_type_email = 'email';

    private string $icon = '&#xe836;';

    public const USER_NAME = 'user_name';
    public const MOBILE = 'mobile';
    public const EMAIL = 'email';

    private static array $commonlyUsedIconName = [
        self::USER_NAME => '姓名',
        self::MOBILE => '手机号',
        self::EMAIL => '邮箱',

    ];

    public function icon(): array
    {
        return [
            'name' => '文本框',
            'type' => $this->getComponentName(),
            'limit' => 0,
            'icon' => $this->icon,
            'sort' => '',
        ];
    }

    public function commonlyUsedIcon(string $alias): array
    {
        $tempName = self::$commonlyUsedIconName[$alias] ?? '';

        if (!$tempName) {
            throw new BusinessException('未设置常用组件');
        }
        $tempValue = $this->parameter();
        $tempValue['is_usually'] = true;
        $tempValue['placeholder'] = '';
        $tempValue['icon'] = $this->icon;
        switch ($alias) {
            case self::USER_NAME:
                $tempValue['name'] = '请填写姓名';
                $tempValue['placeholder'] = '请填写姓名';
                break;
            case self::MOBILE:
                $tempValue['name'] = '请填写手机号';
                $tempValue['placeholder'] = '请填写手机号';
                $tempValue['limit_type'] = 'phone';
                break;
            case self::EMAIL:
                $tempValue['name'] = '请填写邮箱';
                $tempValue['placeholder'] = '请填写邮箱';
                $tempValue['limit_type'] = 'email';
                break;
        }
        return [
            'name' => $tempName,
            'type' => $this->getComponentName(),
            'limit' => 0,
            'icon' => '',
            'sort' => '',
            'value' => $tempValue
        ];
    }

    public function parameter(): array
    {
        return [
            'name' => '',
            'type' => $this->getComponentName(),
            'is_need' => 1,
            'limit_type' => '',
            'limit_number' => 1,
            'limit_type_map' => $this->limitTypeMap(),
            'is_show' => 1,
            'select_options' => [],
            'template_name' => '',
            'template_url' => '',
            'tips' => '',
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
            'limit_type' => 'present|nullable|in:'.implode(',', [$this->limit_type_email, $this->limit_type_phone]),
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
            'limit_type.present' => '板块限制类型 未设置',
            'limit_type.nullable' => '板块限制类型 未设置',
            'limit_type.in' => '板块限制类型 格式不正确',
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
            'limit_type' => $validate['limit_type'],
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
        $res['limit_type_map'] = $this->limitTypeMap();
        $res['icon'] = $this->icon;

        return $res;
    }

    private function limitTypeMap()
    {
        return [
            ['name' => '手机号','value' => $this->limit_type_phone],
            ['name' => '邮箱','value' => $this->limit_type_email],
        ];
    }
}
