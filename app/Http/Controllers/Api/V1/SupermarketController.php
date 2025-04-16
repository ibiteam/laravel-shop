<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RouterEnum;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\SearchDao;
use App\Http\Resources\CommonResourceCollection;
use App\Utils\Constant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 超市
 */
class SupermarketController extends BaseController
{
    public function index(Request $request, SearchDao $search_dao)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string',
                'banner' => 'nullable|string',
                'category_id' => 'nullable|int',
                'page' => 'required|int|min:1',
                'number' => 'required|int|min:1',
            ], [], [
                'title' => '标题',
                'banner' => 'banner',
                'category_id' => '分类ID',
                'page' => '页码',
                'number' => '每页数量',
            ]);

            $title = $validated['title'] ?? RouterEnum::formSource(RouterEnum::SUPERMARKET->value)->getLabel();
            $banner = $validated['banner'] ?? '';
            $search_params = [
                'category_id' => $validated['category_id'] ?? 0,
                'page' => $validated['page'],
                'number' => $validated['number'],
            ];

            $good_list = $search_dao->searchGoods($search_params, get_user()->id ?? 0, Constant::ZERO);

            $data = [
                'title' => $title,
                'banner' => $banner,
                'goods_list' => new CommonResourceCollection($good_list),
            ];

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取超市页面数据异常~');
        }
    }

}
