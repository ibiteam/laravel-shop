<?php

namespace App\Http\Controllers\Manage;

use App\Components\ComponentFactory;
use App\Components\SellerEnter\InputComponent;
use App\Components\SellerEnter\RadioComponent;
use App\Exceptions\BusinessException;
use App\Models\SellerEnterConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * 商家入驻配置.
 */
class SellerEnterConfigController extends BaseController
{
    /**
     * 组件配置.
     *
     * @throws BusinessException
     */
    public function index(Request $request)
    {
        $enterConfigs = SellerEnterConfig::query()->get()
            ->map(function (SellerEnterConfig $sellerEnterConfig) {
                return ComponentFactory::getSellerEnterComponent($sellerEnterConfig->type, $sellerEnterConfig->name)->display($sellerEnterConfig->toArray());
            })->toArray();

        if ($request->expectsJson()) {
            return $this->success($enterConfigs);
        }

        // 左侧组件返回数据
        $basicComponents = [
            SellerEnterConfig::TYPE_TEXT => '文本框',
            SellerEnterConfig::TYPE_RADIO => '单选框',
            SellerEnterConfig::TYPE_CHECKBOX => '多选框',
            SellerEnterConfig::TYPE_SELECT => '下拉框',
            SellerEnterConfig::TYPE_TEXTAREA => '多行文本',
            SellerEnterConfig::TYPE_DATE => '时间',
            SellerEnterConfig::TYPE_FILE => '单文件',
            SellerEnterConfig::TYPE_MORE_FILE => '多文件',
        ];

        $componentIcons = [
            'basic_components' => [],
            'commonly_used_components' => [],
        ];

        $componentValues = [];

        foreach ($basicComponents as $basicComponent => $basicComponentName) {
            $tempBasicComponent = ComponentFactory::getSellerEnterComponent($basicComponent, $basicComponentName);
            $componentIcons['basic_components'][] = $tempBasicComponent->icon();
            $componentValues[] = $tempBasicComponent->parameter();
        }

        $componentIcons['commonly_used_components'][] = ComponentFactory::getSellerEnterComponent(SellerEnterConfig::TYPE_TEXT, '文本框')->commonlyUsedIcon(InputComponent::MOBILE);
        $componentIcons['commonly_used_components'][] = ComponentFactory::getSellerEnterComponent(SellerEnterConfig::TYPE_TEXT, '文本框')->commonlyUsedIcon(InputComponent::EMAIL);
        $componentIcons['commonly_used_components'][] = ComponentFactory::getSellerEnterComponent(SellerEnterConfig::TYPE_RADIO, '单选框')->commonlyUsedIcon(RadioComponent::YES_NO);

        return view('manage.seller_enter_config.index', [
            'component_icons' => $componentIcons,
            'component_values' => $componentValues,
            'content' => $enterConfigs,
        ]);
    }

    /**
     * 配置保存
     * 先删除数据 再更新数据 然后新增数据.
     *
     * @throws BusinessException|\Throwable
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'enter_configs' => 'required|array',
                'enter_configs.*.name' => 'required|string',
                'enter_configs.*.type' => 'required|string',
            ], [
                'enter_configs.required' => '请设置组件',
                'enter_configs.array' => '组件格式不正确!',
                'enter_configs.*.name.required' => '请设置名称',
                'enter_configs.*.name.string' => '名称格式不正确',
                'enter_configs.*.type.required' => '名称类型不能为空',
                'enter_configs.*.type.string' => '名称类型格式不正确',
            ]);
        } catch (ValidationException $validationException) {
            return $this->error($validationException->validator->errors()->first());
        } catch (\Exception $exception) {
            return $this->error('校验异常'.$exception->getMessage());
        }

        $enterConfigsData = $request->input('enter_configs');

        $tempName = array_column($enterConfigsData, 'name');

        if (count($tempName) !== count(array_unique($tempName))) {
            return $this->error('存在名称重复的问题，请调整后重新提交');
        }

        $itemCreatedData = $itemUpdateData = $itemDeletedIds = [];

        $tableItemIds = SellerEnterConfig::query()->get()->pluck('id')->toArray();

        foreach ($enterConfigsData as $index => $inviteItemDatum) {
            $tempItemData = ComponentFactory::getSellerEnterComponent($inviteItemDatum['type'], $inviteItemDatum['name'])->validate($inviteItemDatum);
            $tempItemData['sort'] = $index + 1;

            if (! empty($tableItemIds) && isset($tempItemData['id']) && $tempItemData['id'] > 0 && in_array($tempItemData['id'], $tableItemIds)) {
                $itemUpdateData[] = $tempItemData;

                continue;
            }
            $itemCreatedData[] = $tempItemData;
        }

        if (! empty($tableItemIds)) {
            $requestItemIds = array_column($itemUpdateData, 'id');

            foreach ($tableItemIds as $tableItemId) {
                if (! in_array($tableItemId, $requestItemIds)) {
                    $itemDeletedIds[] = $tableItemId;
                }
            }
        }

        DB::beginTransaction();

        try {
            // 处理需要删除的数据
            if (! empty($itemDeletedIds)) {
                SellerEnterConfig::query()->whereIn('id', $itemDeletedIds)->delete();
            }

            // 处理需要更新的数据
            if (! empty($itemUpdateData)) {
                foreach ($itemUpdateData as $itemUpdateDatum) {
                    SellerEnterConfig::query()->where('id', $itemUpdateDatum['id'])->update($itemUpdateDatum);
                }
            }

            // 处理需要新增的数据
            if (! empty($itemCreatedData)) {
                foreach ($itemCreatedData as $itemCreatedDatum) {
                    SellerEnterConfig::query()->create($itemCreatedDatum);
                }
            }

            DB::commit();

            return $this->success('保存成功');
        } catch (BusinessException $apiException) {
            DB::rollBack();

            return $this->error($apiException->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->error('保存异常'.$exception->getMessage());
        }
    }
}
