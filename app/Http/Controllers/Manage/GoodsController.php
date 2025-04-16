<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\CategoryDao;
use App\Http\Dao\GoodsSkuDao;
use App\Http\Dao\GoodsSpecValueDao;
use App\Http\Requests\Manage\GoodsStoreRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Goods;
use App\Models\GoodsImage;
use App\Models\GoodsParameter;
use App\Models\ShopConfig;
use App\Services\Goods\GoodsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class GoodsController extends BaseController
{
    /**
     * 商品列表.
     */
    public function index(Request $request): JsonResponse
    {
        $goods_id = $request->get('goods_id');
        $category_id = $request->get('category_id');
        $no = $request->get('no');
        $goods_name = $request->get('goods_name');
        $status = (int) $request->get('status', -1);
        $create_start_time = $request->get('create_start_time');
        $create_end_time = $request->get('create_end_time');
        $update_start_time = $request->get('update_start_time');
        $update_end_time = $request->get('update_end_time');
        $number = (int) $request->get('number', 10);

        $list = Goods::query()->withTrashed()->latest()->with('category:id,name')
            ->when($goods_id, fn (Builder $query) => $query->where('id', $goods_id))
            ->when($category_id, fn (Builder $query) => $query->where('category_id', $category_id))
            ->when($no, fn (Builder $query) => $query->where('no', $no))
            ->when($goods_name, fn (Builder $query) => $query->where('name', 'like', "%{$goods_name}%"))
            ->when($status >= 0, fn (Builder $query) => $query->where('status', $status))
            ->when($create_start_time, fn (Builder $query) => $query->where('created_at', '>=', $create_start_time))
            ->when($create_end_time, fn (Builder $query) => $query->where('created_at', '<=', $create_end_time))
            ->when($update_start_time, fn (Builder $query) => $query->where('updated_at', '>=', $update_start_time))
            ->when($update_end_time, fn (Builder $query) => $query->where('updated_at', '<=', $update_end_time))
            ->select(['id', 'name', 'category_id', 'image', 'name', 'sub_name', 'sales_volume', 'no', 'price', 'total', 'sort', 'status', 'created_at', 'updated_at'])
            ->paginate($number);

        $vue_app_url = rtrim(config('host.vue_app_url'), '/');
        $list->getCollection()->transform(function (Goods $goods) use ($vue_app_url) {
            $goods->setAttribute('h5_url', $vue_app_url.'/good?goods_no='.$goods->no);  // 商品h5地址

            return $goods;
        });

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 修改上下架状态.
     */
    public function changeStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '商品ID',
            ]);
            $goods = Goods::query()->whereId($validated['id'])->first();

            if (! $goods instanceof Goods) {
                throw new BusinessException('商品不存在');
            }

            if ($goods->status === Goods::STATUS_ON_SALE) {
                $tmp_status = Goods::STATUS_NOT_SALE;
                $tmp_message = '下架成功';
            } else {
                $tmp_status = Goods::STATUS_ON_SALE;
                $tmp_message = '上架成功';
            }

            if (! $goods->update(['status' => $tmp_status, 'status_datetime' => Carbon::now()->toDateTimeString()])) {
                throw new BusinessException('修改上下架状态失败');
            }
            admin_operation_log( "修改商品：{$goods->name}[{$goods->id}] 上下架状态：{$tmp_message}", AdminOperationLog::TYPE_UPDATE);

            return $this->success('修改成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 修改商品.
     */
    public function edit(Request $request, CategoryDao $category_dao, GoodsSpecValueDao $goods_spec_value_dao, GoodsSkuDao $goods_sku_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '商品ID',
            ]);

            $info = [
                'id' => $validated['id'] ?? 0,
                'category_id' => null,
                'name' => '',
                'label' => '',
                'sub_name' => '',
                'parameters' => [],
                'images' => [],
                'video' => '',
                'video_duration' => 0,
                'content' => '',
                'unit' => '',
                'spec_data' => [],
                'sku_data' => [],
                'price' => 0,
                'integral' => 0,
                'total' => '',
                'type' => Goods::TYPE_DONE_ORDER,
                'status' => Goods::STATUS_ON_SALE,
                'can_quota' => Goods::NOT_QUOTA,
                'quota_number' => 0,
            ];

            if ($validated['id'] > 0) {
                $goods = Goods::query()->with(['images', 'parameters', 'specValues', 'skus', 'detail'])->whereId($validated['id'])->first();

                if (! $goods instanceof Goods) {
                    throw new BusinessException('商品不存在');
                }
                $tmp_images = $goods->images->map(fn (GoodsImage $goodsImage) => $goodsImage->image)->unshift($goods->image);
                // 商品SKU处理
                $info = array_merge($info, [
                    'category_id' => $goods->category_id,
                    'name' => $goods->name,
                    'label' => $goods->label,
                    'sub_name' => $goods->sub_name,
                    'parameters' => $goods->parameters->map(fn (GoodsParameter $goodsParameter) => ['name' => $goodsParameter->name, 'value' => $goodsParameter->value]),
                    'images' => $tmp_images,
                    'video' => $goods->video,
                    'video_duration' => $goods->video_duration,
                    'content' => $goods->detail?->content,
                    'unit' => $goods->unit,
                    'spec_data' => $goods_spec_value_dao->reverseData($goods->specValues),
                    'sku_data' => $goods_sku_dao->reverseData($goods->skus),
                    'price' => $goods->price,
                    'integral' => $goods->integral,
                    'total' => $goods->total,
                    'type' => $goods->type,
                    'status' => $goods->status,
                    'can_quota' => $goods->can_quota,
                    'quota_number' => $goods->quota_number,
                ]);
            }

            return $this->success([
                'category' => $category_dao->getTreeList(),
                'info' => $info,
                'settings' => [
                    'is_open_integral' => shop_config(ShopConfig::IS_OPEN_INTEGRAL),
                ],
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    public function update(GoodsStoreRequest $request, GoodsService $goods_service): JsonResponse
    {
        $validated = $request->validated();

        try {
            if ($validated['can_quota'] && ! $validated['quota_number']) {
                throw new BusinessException('当开启商品限购时，商品限购数量必须 大于等于 1');
            }

            $goods_service->storeOrUpdate($this->adminUser(), $request->validated(), $validated['id']);

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
