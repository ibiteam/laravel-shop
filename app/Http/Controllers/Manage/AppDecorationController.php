<?php

namespace App\Http\Controllers\Manage;

use App\Components\ComponentFactory;
use App\Components\PageDefaultDict;
use App\Enums\ConstantEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\ProcessDataException;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AppDecoration;
use App\Models\AppDecorationItem;
use App\Models\AppDecorationItemDraft;
use App\Models\AppDecorationLog;
use App\Models\Goods;
use App\Models\ShopConfig;
use App\Services\AppDecoration\AppDecorationLogService;
use App\Services\AppDecoration\AppDecorationService;
use App\Services\Goods\GoodsService;
use App\Services\RouterService;
use App\Utils\Constant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AppDecorationController extends BaseController
{
    public function index(Request $request)
    {
        $query = AppDecoration::query()
            ->with('adminUser:id,user_name')
            ->withCount('children')
            ->orderBy('id')
            ->whereParentId(Constant::ZERO);
        $list = $query->paginate($request->input('number', 10));
        $list->getCollection()->transform(function (AppDecoration $app_decoration) {
            $app_decoration->path = $app_decoration->url;

            return $app_decoration;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    public function decoration(Request $request, AppDecorationService $app_decoration_service, RouterService $router_service)
    {
        $id = $request->input('id');
        $app_decoration = AppDecoration::query()->whereId($id)->first();

        if (! $app_decoration) {
            return $this->error('未找到网站装修信息');
        }
        $app_website_data = $app_decoration->toArray();
        // 查询历史记录中最新一条 对应的草稿数据
        $item_data = $app_decoration_service->getLatestDraftItems($app_decoration);

        /* 不参与循环的组件 - 固定组件 */
        $not_for_names = [
            AppDecoration::ALIAS_HOME => [
                AppDecorationItem::COMPONENT_NAME_DANPING_ADVERTISEMENT,
                AppDecorationItem::COMPONENT_NAME_SUSPENDED_ADVERTISEMENT,
                AppDecorationItem::COMPONENT_NAME_HOME_NAV,
                //                AppDecorationItem::COMPONENT_NAME_LABEL,
            ],
        ];

        try {
            [$component_icon, $component_value, $not_items_fixed_value] = app(PageDefaultDict::class)->commonMap($app_decoration->alias);

            if ($item_data->isEmpty()) {
                // 无关联数据 取固定组件数据
                $temp_data = $not_items_fixed_value;
            } else {
                // 有关联数据 查询重组关联数据
                $temp_data = $item_data->map(function (AppDecorationItemDraft $item) {
                    return ComponentFactory::getComponent($item->component_name)->display($item->toArray());
                })->toArray();
            }
            $temp_data = collect($temp_data)->values();
            // 可多个组件的数据
            $data = $temp_data->whereNotIn('component_name', $not_for_names[$app_decoration->alias] ?? [])->values()->toArray();
            // 不可多个组件的数据
            $not_for_data = $temp_data->whereIn('component_name', $not_for_names[$app_decoration->alias] ?? [])->values()->toArray();
            // 预览地址
            $preview_path = $router_service->getRouterPath(AppDecoration::$path[$app_decoration->alias], ['id' => $id]);

            return $this->success(compact('component_icon', 'data', 'component_value', 'not_for_data', 'app_website_data', 'preview_path'));
        } catch (\Exception $exception) {
            if ($exception instanceof BusinessException) {
                return $this->error($exception->getMessage());
            }
            Log::error($exception->getMessage());

            return $this->error('初始化失败');
        }
    }

    // 保存草稿
    public function decorationSave(Request $request, AppDecorationLogService $app_decoration_log_service, AppDecorationService $app_decoration_service)
    {
        $admin_user_id = get_admin_user()?->id ?: 0;

        try {
            // 获取请求参数
            $validated = $request->validate([
                'id' => 'required|integer|exists:\App\Models\AppDecoration,id',
                'title' => 'required|string',
                'keywords' => 'required|string',
                'description' => 'required|string',
                'button_type' => 'required|integer|in:'.AppDecoration::OPERATE_TYPE_RELEASE.','.AppDecoration::OPERATE_TYPE_PREVIEW.','.AppDecoration::OPERATE_TYPE_SAVE_DRAFT,
            ], [], [
                'id' => '装修页面ID',
                'title' => 'TDK标题',
                'keywords' => 'TDK关键词',
                'description' => 'TDK描述',
                'button_type' => '操作类型',
            ]);
            $button_type = $validated['button_type'];
            // 获取装修信息
            $app_decoration = $app_decoration_service->getAppDecoration($validated['id']);

            $data = json_decode($request->get('data'), true);
            // 校验组件数据
            $app_decoration_service->validateComponentData($app_decoration, $data);

            // 处理新增和更新的数据
            [$insert_data, $update_data] = $this->processComponentData($data, $button_type);

            DB::beginTransaction();

            // 插入新数据
            $insertedIds = $this->handleInserts($app_decoration, $insert_data);
            // 更新已有数据
            $updatedIds = $this->handleUpdates($app_decoration, $update_data);
            // 合并所有组件 ID
            $item_ids = array_merge($insertedIds, $updatedIds);

            // 更新装修信息
            $app_decoration->admin_user_id = $admin_user_id;
            $app_decoration->title = $validated['title'];
            $app_decoration->keywords = $validated['keywords'];
            $app_decoration->description = $validated['description'];
            $app_decoration->save();
            // 处理日志记录
            $app_decoration_service->handleLog($app_decoration_log_service, $app_decoration, $item_ids, $button_type, $admin_user_id);

            DB::commit();
        } catch (ValidationException $validation_exception) {
            return $this->failed([
                'id' => 0,
            ], $validation_exception->validator->errors()->first(), ConstantEnum::DECORATION_COMPONENT);
        } catch (ModelNotFoundException $e) {
            return $this->error('未找到网站装修信息');
        } catch (ProcessDataException $process_data_exception) {
            return $this->failed($process_data_exception->getData(), $process_data_exception->getMessage(), ConstantEnum::DECORATION_COMPONENT);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->error('装修失败，请稍后重试'.$exception->getMessage());
        }

        return $this->success([]);
    }

    // 历史记录
    public function decorationHistory(Request $request, AppDecorationLogService $app_decoration_log_service)
    {
        $log_data = $app_decoration_log_service->getAllLogsPaginate((int) $request->get('id')); // 装修页面id

        return $this->success($log_data);
    }

    // 还原历史记录
    public function historyRestore(Request $request)
    {
        $log_id = $request->get('log_id'); // 装修记录id

        if (! AppDecorationLog::whereId($log_id)->update(['updated_at' => now()])) {
            return $this->error('还原失败');
        }

        return $this->success('保存成功');
    }

    // 为您推荐组件数据
    public function recommendData(GoodsService $goods_service)
    {
        return $this->success($goods_service->getRecommendData());
    }

    // 商品推荐组件 - 智能推荐数据
    public function goodsForIntelligent(Request $request, GoodsService $goods_service)
    {
        $sort_type = $request->get('sort_type') ?: AppDecorationItem::SORT_SALES;
        $number = (int) $request->get('number') ?: 3;
        $goods_data = $goods_service->getRecommendGoods(limit: $number, sort_type: $sort_type);

        return $this->success($goods_data);
    }

    // 推荐商品列表
    public function goodsList(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $goods_id = $request->get('goods_id', 0);
        $category_id = $request->get('category_id', 0);
        $number = $request->get('number', 10);
        $is_show_sales_volume = shop_config(ShopConfig::IS_SHOW_SALES_VOLUME);
        $data = Goods::query()
            ->when($goods_id, fn ($query) => $query->whereId($goods_id))
            ->when($category_id, fn ($query) => $query->whereCategoryId($category_id))
            ->when($keywords, fn ($query) => $query->where(function ($query) use ($keywords) {
                $query->where('no', 'like', "%{$keywords}%")
                    ->orWhere('name', 'like', "%{$keywords}%");
            }))
            ->show()
            ->select('no', 'name', 'image', 'price', 'integral', 'total', 'label', 'sub_name')
            ->addSelect(DB::raw("CASE WHEN {$is_show_sales_volume} THEN sales_volume ELSE NULL END AS sales_volume"))
            ->latest()->paginate($number);

        return $this->success(new CommonResourceCollection($data));
    }

    // 导入商品
    public function importGoods(Request $request)
    {
        $goods_ids = $request->get('goods_ids', []);
        $goods_nos = $request->get('goods_nos', []);
        $is_show_sales_volume = shop_config(ShopConfig::IS_SHOW_SALES_VOLUME);
        $data = Goods::query()
            ->show()
            ->where(fn ($query) => $query->whereIn('id', $goods_ids)->orWhereIn('no', $goods_nos))
            ->select('no', 'name', 'image', 'price', 'integral', 'total', 'label', 'sub_name')
            ->addSelect(DB::raw("CASE WHEN {$is_show_sales_volume} THEN sales_volume ELSE NULL END AS sales_volume"))
            ->latest()->get();

        if (count($data) > 20) {
            return $this->error('导入商品加上已选商品不能超过 20 个');
        }

        return $this->success($data);
    }

    /**
     * 处理组件数据，区分插入和更新.
     */
    private function processComponentData(array $data, int $button_type): array
    {
        $insert_data = [];
        $update_data = [];

        foreach ($data as $key => $datum) {
            if (! isset($datum['component_name'])) {
                continue;
            }

            $temp_item = ComponentFactory::getComponent($datum['component_name'], $datum['name'])->validate($datum);
            $temp_item['sort'] = $key + 1;
            $temp_item['id'] = is_numeric($temp_item['id']) ? $temp_item['id'] : 0;

            if ($temp_item['id'] > 0 && $button_type == AppDecoration::OPERATE_TYPE_RELEASE) {
                $update_data[] = $temp_item;
            } else {
                $temp_item['id'] = 0;
                $insert_data[] = $temp_item;
            }
        }

        return [$insert_data, $update_data];
    }

    /**
     * 处理新增数据.
     */
    private function handleInserts(AppDecoration $app_decoration, array $insert_data): array
    {
        $insertedIds = [];

        foreach ($insert_data as $insert_datum) {
            $insertedModel = $app_decoration->itemDraft()->create($insert_datum);
            $insertedIds[] = $insertedModel->id;
        }

        return $insertedIds;
    }

    /**
     * 处理更新数据.
     */
    private function handleUpdates(AppDecoration $app_decoration, array $update_data): array
    {
        $updatedIds = [];

        foreach ($update_data as $update_datum) {
            $app_decoration->itemDraft()->where('id', $update_datum['id'])->update($update_datum);
            $updatedIds[] = $update_datum['id'];
        }

        return $updatedIds;
    }
}
