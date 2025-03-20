<?php

namespace App\Components;

use App\Components\SellerEnter\CheckboxComponent;
use App\Components\SellerEnter\DateComponent;
use App\Components\SellerEnter\FileComponent;
use App\Components\SellerEnter\InputComponent;
use App\Components\SellerEnter\MoreFileComponent;
use App\Components\SellerEnter\RadioComponent;
use App\Components\SellerEnter\SelectComponent;
use App\Components\SellerEnter\TextareaComponent;
use App\Exceptions\BusinessException;
use App\Models\SellerEnterConfig;


class ComponentFactory
{

    /**
     * @throws BusinessException
     */
    public static function getSellerEnterComponent(string $component_name, string $name = '')
    {
        $components = [
            SellerEnterConfig::TYPE_TEXT => InputComponent::class,
            SellerEnterConfig::TYPE_FILE => FileComponent::class,
            SellerEnterConfig::TYPE_MORE_FILE => MoreFileComponent::class,
            SellerEnterConfig::TYPE_SELECT => SelectComponent::class,
            SellerEnterConfig::TYPE_RADIO => RadioComponent::class,
            SellerEnterConfig::TYPE_CHECKBOX => CheckboxComponent::class,
            SellerEnterConfig::TYPE_DATE => DateComponent::class,
            SellerEnterConfig::TYPE_TEXTAREA => TextareaComponent::class,
        ];
        if (!isset($components[$component_name])) {
            throw new BusinessException('没有找到' . $component_name . '组件！');
        }
        $class = $components[$component_name];
        $page_component = new $class($component_name, $name);
        //实例化组件
        if (!$page_component instanceof PageComponent) {
            throw new BusinessException($class . ' is not instance of PageComponent');
        }
        return $page_component;
    }

}
