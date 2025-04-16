<?php

namespace App\Http\Controllers\Manage\Article;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Dao\ArticleCategoryDao;
use App\Models\AdminOperationLog;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 文章分类
class ArticleCategoryController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $name = $request->get('name');

        $data = ArticleCategory::query()
            ->when($name, fn ($query) => $query->where('name', 'like', '%'.$name.'%'))
            ->with(['allChildren'])->whereParentId(0)
            ->orderByDesc('sort')
            ->orderByDesc('id')
            ->get();

        return $this->success($data);
    }

    /**
     * 获取信息.
     */
    public function info(Request $request, ArticleCategoryDao $article_category_dao)
    {
        $id = $request->get('id') ?? 0;

        $tree_categories = $article_category_dao->getTreeList();

        $info = null;

        if ($id) {
            $info = ArticleCategory::query()->whereId($id)->first();

            if (! $info instanceof ArticleCategory) {
                return $this->error('数据不存在');
            }
            $info->type = strval($info->type);
        }

        return $this->success([
            'tree_categories' => $tree_categories,
            'info' => $info,
        ]);
    }

    /**
     * 添加/编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'parent_id' => 'required|integer',
                'name' => 'required|string',
                'alias' => 'nullable|string',
                'title' => 'required|string',
                'description' => 'required|string',
                'keywords' => 'required|string',
                'type' => 'required|in:1,2',
                'sort' => 'required|integer',
                'is_show' => 'required|boolean',
            ], [], [
                'id' => '分类ID',
                'name' => '分类名称',
                'alias' => '分类别名',
                'title' => '分类标题',
                'description' => '分类描述',
                'keywords' => '分类关键词',
                'type' => '类型',
                'sort' => '排序',
                'is_show' => '是否显示',
            ]);

            $id = $validated['id'] ?? 0;
            $alias = $validated['alias'] ?? null;

            if ($id) {
                $article_category = ArticleCategory::whereId($id)->first();

                if (! $article_category) {
                    throw new BusinessException('文章分类不存在');
                }

                if (ArticleCategory::where('id', '!=', $id)->whereName($validated['name'])->first()) {
                    throw new BusinessException('文章分类名称已存在');
                }

                if ($alias && ArticleCategory::where('id', '!=', $id)->whereAlias($alias)->first()) {
                    throw new BusinessException('文章分类别名已存在');
                }
            } else {
                $article_category = new ArticleCategory;

                if (ArticleCategory::whereName($validated['name'])->first()) {
                    throw new BusinessException('文章分类名称已存在');
                }

                if ($alias && ArticleCategory::whereAlias($alias)->first()) {
                    throw new BusinessException('文章分类别名已存在');
                }
            }

            $article_category->parent_id = $validated['parent_id'];
            $article_category->name = $validated['name'];
            $article_category->alias = $alias;
            $article_category->title = $validated['title'];
            $article_category->description = $validated['description'];
            $article_category->keywords = $validated['keywords'];
            $article_category->type = $validated['type'];
            $article_category->sort = $validated['sort'];
            $article_category->is_show = intval($validated['is_show']);

            if (! $article_category->save()) {
                throw new BusinessException('保存失败');
            }

            if ($id) {
                $log = "编辑文章分类[id:{$article_category->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($article_category->getChanges()), $article_category->getChanges()));
                admin_operation_log($log, AdminOperationLog::TYPE_UPDATE);
            } else {
                $log = "新增文章分类[id:{$article_category->id}]";
                admin_operation_log($log, AdminOperationLog::TYPE_STORE);
            }

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('文章分类操作异常~');
        }
    }

    /**
     * 删除.
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '分类ID',
            ]);
            $route_category = ArticleCategory::query()->whereId($validated['id'])->first();

            if (! $route_category instanceof ArticleCategory) {
                return $this->error('数据不存在');
            }

            if (ArticleCategory::query()->whereParentId($route_category->id)->exists()) {
                return $this->error('请先删除子分类');
            }

            if (Article::query()->whereArticleCategoryId($route_category->id)->exists()) {
                return $this->error('请先删除该分类下的文章');
            }

            if ($route_category->delete()) {
                admin_operation_log("删除了文章分类:{$route_category->name}[{$route_category->id}]", AdminOperationLog::TYPE_DESTROY);

                return $this->success('删除成功');
            }

            throw new BusinessException('删除失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
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
                'id' => '文章分类ID',
                'is_show' => '是否显示',
            ]);

            $article_category = ArticleCategory::query()->whereId($validated['id'])->first();

            if (! $article_category) {
                throw new BusinessException('文章分类不存在');
            }

            $article_category->is_show = $validated['is_show'];

            if (! $article_category->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改文章分类显示隐藏[id:{$validated['id']}]".implode(
                ',',
                array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($article_category->getChanges()), $article_category->getChanges())
            );
            admin_operation_log($log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换是否展示异常~');
        }
    }

    /**
     * 转移文章.
     */
    public function move(Request $request)
    {
        try {
            $validated = $request->validate([
                'old_category_id' => 'required|integer',
                'new_category_id' => 'required|integer',
            ], [], [
                'old_category_id' => '需要转移的分类ID',
                'new_category_id' => '目标分类ID',
            ]);

            $old_category_id = $validated['old_category_id'];
            $new_category_id = $validated['new_category_id'];

            if ($old_category_id == $new_category_id) {
                throw new BusinessException('分类相同，无需转移');
            }

            $old_article_cat = ArticleCategory::whereId($old_category_id)->with('articles')->first();
            $new_article_cat = ArticleCategory::whereId($new_category_id)->with('articles')->first();

            if (! $old_article_cat) {
                throw new BusinessException('待转移的分类不存在');
            }

            if (! $new_article_cat) {
                throw new BusinessException('目标分类不存在');
            }

            // 查询分类下文章直接转移
            $article = Article::whereArticleCategoryId($old_category_id)->get();

            if ($article->isEmpty()) {
                throw new BusinessException('待转移的分类下没有文章，不需要转移');
            }

            $flag = Article::whereArticleCategoryId($old_category_id)->update(['article_category_id' => $new_category_id]);

            if (! $flag) {
                throw new BusinessException('转移失败');
            }

            return $this->success('转移成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('转移文章异常~');
        }
    }
}
