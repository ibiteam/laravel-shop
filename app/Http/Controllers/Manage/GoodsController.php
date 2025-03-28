<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Dao\CategoryDao;
use App\Http\Dao\GoodsParameterTemplateDao;
use App\Http\Dao\GoodsSkuDao;
use App\Http\Dao\GoodsSkuTemplateDao;
use App\Http\Dao\GoodsSpecValueDao;
use App\Http\Requests\Manage\GoodsStoreRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Goods;
use App\Models\GoodsImage;
use App\Models\GoodsParameter;
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
        $number = (int) $request->get('limit', 10);

        $list = Goods::query()->latest()->with('category:id,name')
            ->when($goods_id, fn (Builder $query) => $query->where('id', $goods_id))
            ->when($category_id, fn (Builder $query) => $query->where('category_id', $category_id))
            ->when($no, fn (Builder $query) => $query->where('no', $no))
            ->when($goods_name, fn (Builder $query) => $query->where('name', 'like', "%{$goods_name}%"))
            ->when($status >= 0, fn (Builder $query) => $query->where('status', $status))
            ->when($create_start_time, fn (Builder $query) => $query->where('created_at', '>=', $create_start_time))
            ->when($create_end_time, fn (Builder $query) => $query->where('created_at', '<=', $create_end_time))
            ->when($update_start_time, fn (Builder $query) => $query->where('updated_at', '>=', $update_start_time))
            ->when($update_end_time, fn (Builder $query) => $query->where('updated_at', '<=', $update_end_time))
            ->select(['id', 'name', 'category_id', 'image', 'name', 'sub_name', 'no', 'price', 'total', 'sort', 'status', 'created_at', 'updated_at'])
            ->paginate($number);

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
            admin_operation_log($this->adminUser(), "修改商品：{$goods->name}[{$goods->id}] 上下架状态：{$tmp_message}", AdminOperationLog::TYPE_UPDATE);

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
    public function edit(Request $request, CategoryDao $category_dao, GoodsSpecValueDao $goods_spec_value_dao, GoodsSkuDao $goods_sku_dao, GoodsParameterTemplateDao $goods_parameter_dao, GoodsSkuTemplateDao $goods_sku_template_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '商品ID',
            ]);

            $info = [
                'category_id' => null,
                'name' => '',
                'label' => '',
                'sub_name' => '',
                'parameters' => [],
                'images' => [],
                'video' => '',
                'video_duration' => '',
                'content' => '',
                'unit' => '',
                'spec_data' => [],
                'sku_data' => [],
                'price' => '',
                'total' => '',
                'type' => '',
                'status' => Goods::STATUS_ON_SALE,
                'can_quota' => Goods::NOT_QUOTA,
                'quota_number' => 0,
            ];

            if ($validated['id'] > 0) {
                $goods = Goods::query()->with(['images', 'parameters', 'specValues', 'skus', 'detail'])->whereId($validated['id'])->first();

                if (! $goods instanceof Goods) {
                    throw new BusinessException('商品不存在');
                }
                $tmp_images = $goods->images->map(fn (GoodsImage $goodsImage) => ['image' => $goodsImage->image, 'type' => 'detail']);
                $tmp_images->unshift(['image' => $goods->image, 'type' => 'main']);
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
                'parameter_template' => $goods_parameter_dao->list(),
                'sku_template' => $goods_sku_template_dao->list(),
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败'.$throwable->getMessage());
        }
    }

    public function store(GoodsStoreRequest $request, GoodsService $goods_service): JsonResponse
    {
        $validated = $request->validated();

        try {
            $goods_service->storeOrUpdate($this->adminUser(), $request->validated(), $validated['goods_sn']);

            return $this->success('操作成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (\Throwable $throwable) {
            return $this->error('添加失败'.$throwable->getMessage());
        }
    }
}
