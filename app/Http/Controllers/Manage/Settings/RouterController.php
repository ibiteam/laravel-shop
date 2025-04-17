<?php

namespace App\Http\Controllers\Manage\Settings;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Router;
use App\Models\RouterCategory;
use App\Services\RouterService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 访问地址
class RouterController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request, RouterService $router_service)
    {
        $name = $request->get('name');
        $alias = $request->get('alias');
        $router_category_id = intval($request->get('router_category_id'));
        $is_show = intval($request->get('is_show'));
        $number = (int) $request->get('number', 10);
        $keywords = $request->get('keywords', '');

        $data = Router::query()->with('routerCategory')
            ->when($keywords, fn ($query) => $query->where(function ($query) use ($keywords) {
                $query->where('name', 'like', "%{$keywords}%")
                    ->orWhere('alias', 'like', "%{$keywords}%");
            }))
            ->when($name, fn ($query) => $query->where('name', 'like', '%'.$name.'%'))
            ->when($alias, fn ($query) => $query->where('alias', 'like', '%'.$alias.'%'))
            ->when($router_category_id, fn ($query) => $query->where('router_category_id', '=', $router_category_id))
            ->when($is_show > -1, fn ($query) => $query->where('is_show', '=', $is_show))
            ->orderByDesc('sort')->orderByDesc('id')->paginate($number);

        $data->getCollection()->transform(function (Router $router) use ($router_service) {
            return [
                'id' => $router->id,
                'category_name' => $router->routerCategory?->name,
                'router_category_id' => $router->router_category_id,
                'name' => $router->name,
                'alias' => $router->alias,
                'h5_url_show' => $router->h5_url,
                'h5_url' => $router_service->getRouterPath($router->alias),
                'params' => $router->params ? json_encode($router->params, JSON_UNESCAPED_UNICODE) : '',
                'is_show' => $router->is_show,
                'sort' => $router->sort,
                'created_at' => $router->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $router->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 访问地址分类.
     */
    public function categories()
    {
        $link_categories = RouterCategory::query()
            ->whereType(RouterCategory::TYPE_LINK)
            ->whereIsShow(RouterCategory::IS_SHOW_YES)
            ->orderByDesc('sort')
            ->get(['id as value', "name as label"]);
        return $this->success($link_categories);
    }

    /**
     * 添加编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'router_category_id' => 'required|integer',
                'name' => 'required|string',
                'alias' => 'required|string',
                'h5_url' => 'required|string',
                'params' => 'nullable|json',
                'sort' => 'nullable|integer',
                'is_show' => 'required|boolean',
            ], [], [
                'id' => '访问地址ID',
                'router_category_id' => '分类ID',
                'name' => '访问地址名称',
                'alias' => '访问地址别名',
                'h5_url' => 'H5地址',
                'params' => '额外参数',
                'is_show' => '是否显示',
                'sort' => '排序',
            ]);

            $validated['id'] = $validated['id'] ?? 0;

            if ($validated['id']) {
                $router = Router::whereId($validated['id'])->first();

                if (! $router) {
                    throw new BusinessException('访问地址不存在');
                }

                if (Router::where('id', '!=', $validated['id'])->whereName($validated['name'])->first()) {
                    throw new BusinessException('访问地址名称已存在');
                }

                if (Router::where('id', '!=', $validated['id'])->whereAlias($validated['alias'])->first()) {
                    throw new BusinessException('访问地址别名已存在');
                }
            } else {
                $router = new Router;

                if (Router::whereName($validated['name'])->first()) {
                    throw new BusinessException('访问地址名称已存在');
                }

                if (Router::whereAlias($validated['alias'])->first()) {
                    throw new BusinessException('访问地址别名已存在');
                }
            }

            $params = $validated['params'] ?? null;
            if ($params !== null) {
                $decodedParams = json_decode($params, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new BusinessException('额外参数必须是有效的 JSON 格式');
                }
                if (!is_array($decodedParams) || array_values($decodedParams) === $decodedParams) {
                    throw new BusinessException('额外参数必须是 JSON 键值对');
                }
                $params = $decodedParams;
            }

            $router->router_category_id = $validated['router_category_id'];
            $router->name = $validated['name'];
            $router->alias = $validated['alias'];
            $router->h5_url = $validated['h5_url'];
            $router->params = $params;
            $router->sort = $validated['sort'] ?? 0;
            $router->is_show = intval($validated['is_show']);

            if (! $router->save()) {
                throw new BusinessException('保存失败');
            }

            $log = "新增访问地址[id:{$router->id}]";

            if ($validated['id']) {
                $log = "编辑访问地址[id:{$router->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($router->getChanges()), $router->getChanges()));
            }
            admin_operation_log( $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('访问地址操作异常~'.$throwable->getMessage());
        }
    }

    /**
     * 切换是否显示.
     */
    public function changeShow(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'is_show' => 'required|integer|in:0,1',
            ], [], [
                'id' => '访问地址ID',
                'is_show' => '是否显示',
            ]);

            $router = Router::query()->whereId($validated['id'])->first();

            if (! $router) {
                throw new BusinessException('访问地址不存在');
            }
            $router->is_show = $validated['is_show'];

            if (! $router->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改访问地址显示隐藏[id:{$validated['id']}]".implode(
                ',',
                array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($router->getChanges()), $router->getChanges())
            );
            admin_operation_log( $log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换是否展示异常~');
        }
    }
}
