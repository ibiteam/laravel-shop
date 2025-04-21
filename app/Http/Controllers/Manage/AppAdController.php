<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\BusinessException;
use App\Http\Requests\Manage\AppAdRequest;
use App\Http\Requests\Manage\UserAddressRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdCate;
use App\Models\AdminOperationLog;
use App\Models\AppAd;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppAdController extends BaseController
{
    // 分类名称列表
    public function getCateNames()
    {
        $adCateName = 'app_cate';
        $adCateNames = AdCate::getCateNames(AdCate::MOBILE_TYPE);

        return $this->success(compact('adCateName', 'adCateNames'));
    }

    // 获取分类信息
    public function getCates(Request $request)
    {
        $activeName = $request->get('activeName');
        $name = $request->get('name');
        $data = AdCate::getCates($activeName, $name);

        return $this->success($data);
    }

    // 更新分类信息
    public function updateCate(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $cate = AdCate::query()->whereId($id)->first();
        if (!$cate) {
            return $this->error('此分类不存在');
        }
        $cate->name = $name;
        if (!$cate->save()) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }

    // 获取广告信息
    public function getAppAds(Request $request)
    {
        $cate_id = $request->get('cate_id');
        $data = [];
        $ad_cate = AdCate::query()->whereId($cate_id)->first();
        if ($ad_cate) {
            $advert = AppAd::query()->whereAdCateId($cate_id)->paginate($request->get('number'));
            $data = (new CommonResourceCollection($advert))->toArray($request);
        }
        $data['ad_cate'] = $ad_cate;

        return $this->success($data);
    }

    // 更新广告信息
    public function update(AppAdRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $operation = '添加';
        $operation_type = AdminOperationLog::TYPE_STORE;

        if ($id = $validated['id']) {
            $app_ad = AppAd::query()->find($id);

            if (! $app_ad instanceof AppAd) {
                return $this->error('广告不存在');
            }

            $operation = '修改';
            $operation_type = AdminOperationLog::TYPE_UPDATE;
        } else {
            $app_ad = new AppAd;
        }
        $app_ad->id = $id;
        $app_ad->name = $validated['name'];
        $app_ad->image = $validated['image'];
        $app_ad->sort = $validated['sort'];
        $app_ad->link_type = $validated['link_type'];
        $app_ad->link = $validated['link'];
        $app_ad->is_show = $validated['is_show'];
        $app_ad->type = $validated['type'];
        $app_ad->start_time = $validated['start_time'];
        $app_ad->end_time = $validated['end_time'];

        if (! $app_ad->save()) {
            return $this->error('保存失败');
        }
        // 记录日志
        admin_operation_log( "{$operation}了广告:[{$app_ad->id}]", $operation_type);

        return $this->success('保存成功');
    }

    // 更换广告图
    public function updateAdImage(Request $request)
    {
        $id = $request->get('id');
        $image = $request->get('image');
        $app_ad = AppAd::query()->find($id);
        if (!$app_ad) {
            return $this->error('此分类不存在');
        }
        $app_ad->image = $image;
        if (!$app_ad->save()) {
            return $this->error('保存失败');
        }

        return $this->success('保存成功');
    }

    // 切换状态
    public function toggleStatus(Request $request)
    {
        $sign = $request->input('sign', 0); //标识（操作）
        $status = $request->input('val', 0); //状态值
        $id = $request->input('id', 0);
        $app_ad = AppAd::whereId($id)->first();
        if (!$app_ad) {
            return $this->error('此广告不存在');
        }
        $app_ad->$sign = $status;
        if ($app_ad->save()) {
            return $this->success('保存成功');
        }

        return $this->error('保存失败！');
    }

    // 删除广告
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $app_ad = AppAd::whereId($id)->first();
        if (!$app_ad) {
            return $this->error('此广告不存在');
        }
        if ($app_ad->delete()) {
            admin_operation_log( "删除了广告:[{$app_ad->id}]", AdminOperationLog::TYPE_DESTROY);
            return $this->success('删除成功');
        }

        return $this->error('删除失败！');
    }
}
