<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\SearchDao;
use App\Http\Dao\SearchKeywordDao;
use App\Http\Resources\CommonResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SearchController extends BaseController
{
    /**
     * 搜索商品.
     */
    public function searchGoods(Request $request, SearchDao $search_dao)
    {
        $user = $this->user();

        try {
            $validated = $request->validate([
                'keywords' => 'nullable|string|required_without:category_id',
                'category_id' => 'nullable|int|required_without:keywords',
                'min_price' => 'nullable|numeric',
                'max_price' => 'nullable|numeric',
                'sort_type' => 'nullable|string',
                'page' => 'required|int|min:1',
                'number' => 'required|int|min:1',
            ], [], [
                'keywords' => '搜索关键字',
                'category_id' => '分类ID',
                'min_price' => '最小价格',
                'max_price' => '最大价格',
                'sort_type' => '排序类型',
                'page' => '页码',
                'number' => '每页数量',
            ]);

            // 搜索逻辑
            $list = $search_dao->searchGoods($validated, $user->id ?? 0);

            return $this->success(new CommonResourceCollection($list));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('搜索商品异常~');
        }
    }

    /**
     * 推荐关键字（输入搜索关键字后提示的）.
     */
    public function getKeywords(Request $request, SearchKeywordDao $search_keyword_dao)
    {
        try {
            $validated = $request->validate([
                'keywords' => 'required|string',
            ], [], [
                'keywords' => '关键字',
            ]);

            $keywords = $validated['keywords'];

            return $this->success($search_keyword_dao->getKeywords($keywords));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取推荐关键字异常');
        }
    }
}
