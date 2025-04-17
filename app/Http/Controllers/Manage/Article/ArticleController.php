<?php

namespace App\Http\Controllers\Manage\Article;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Dao\ArticleCategoryDao;
use App\Http\Resources\CommonResourceCollection;
use App\Models\AdminOperationLog;
use App\Models\Article;
use App\Models\ArticleContent;
use App\Models\ArticleCover;
use App\Services\HTMLPurifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// 文章列表
class ArticleController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $title = $request->get('title');
        $author = $request->get('author');
        $article_category_id = intval($request->get('article_category_id'));
        $admin_user_name = $request->get('admin_user_name');
        $is_top = $request->get('is_top');
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $number = (int) $request->get('number', 10);

        $data = Article::query()->with(['articleCategory', 'adminUser'])
            ->withCount(['articleViews'])
            ->when($title, fn ($query) => $query->where('title', 'like', '%'.$title.'%'))
            ->when($author, fn ($query) => $query->where('author', 'like', '%'.$author.'%'))
            ->when($article_category_id, fn ($query) => $query->where('article_category_id', '=', $article_category_id))
            ->when($admin_user_name, fn ($query) => $query->whereHas('adminUser', fn ($query) => $query->where('user_name', 'like', '%'.$admin_user_name.'%')))
            ->when(! is_null($is_top), fn ($query) => $query->where('is_top', '=', $is_top))
            ->when($start_time, fn ($query) => $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($end_time))))
            ->orderByDesc('sort')->orderByDesc('id')->paginate($number);
        $data->getCollection()->transform(function (Article $article) {
            return [
                'id' => $article->id,
                'category_name' => $article->articleCategory?->name,
                'article_category_id' => $article->article_category_id,
                'title' => $article->title,
                'cover' => $article->cover,
                'author' => $article->author,
                'is_show' => $article->is_show,
                'is_top' => $article->is_top,
                'is_login' => $article->is_login,
                'click_count' => $article->click_count,
                'article_view_count' => $article->article_views_count,
                'sort' => $article->sort,
                'admin_user_name' => $article->adminUser?->user_name,
                'created_at' => $article->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $article->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    /**
     * 获取信息.
     */
    public function info(Request $request, ArticleCategoryDao $article_category_dao)
    {
        try {
            $validated = $request->validate([
                'id' => 'nullable|integer',
            ], [], [
                'id' => '文章ID',
            ]);

            $id = $validated['id'] ?? 0;

            $article = null;

            if ($id) {
                $article = Article::query()->whereId($id)->first();

                if (! $article instanceof Article) {
                    throw new BusinessException('文章不存在');
                }

                $article->content = $article->articleContent->content ?? '';
            }

            $tree_categories = $article_category_dao->getTreeList();

            $article_covers = ArticleCover::query()->select(['id', 'img_url'])->get()->toArray();

            return $this->success([
                'tree_categories' => $tree_categories,
                'article_covers' => $article_covers,
                'article' => $article,
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取信息异常');
        }
    }

    /**
     * 添加 编辑.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'nullable|integer',
                'content' => 'required|string',
                'article_category_id' => 'required|integer',
                'title' => 'required|string',
                'cover' => 'required|string',
                'keywords' => 'required|string',
                'description' => 'nullable|string',
                'is_top' => 'required|boolean',
                'is_login' => 'required|boolean',
                'is_show' => 'required|boolean',
                'is_recommend' => 'required|boolean',
                'author' => 'nullable|string',
                'sort' => 'nullable|integer',
                'click_count' => 'nullable|integer',
                'file_url' => 'nullable|string',
                'goods_category_id' => 'nullable|integer',
            ], [], [
                'id' => '文章ID',
                'content' => '内容',
                'article_category_id' => '分类ID',
                'title' => '标题',
                'cover' => '封面',
                'keywords' => '关键词',
                'description' => '描述',
                'is_top' => '是否置顶',
                'is_login' => '是否需要登录',
                'is_show' => '是否显示',
                'is_recommend' => '是否推荐',
                'author' => '作者',
                'sort' => '排序',
                'click_count' => '点击次数',
                'file_url' => '文件路径',
                'goods_category_id' => '商品分类ID',
            ]);

            $id = $validated['id'] ?? 0;

            if ($id) {
                $article = Article::whereId($id)->first();

                if (! $article) {
                    throw new BusinessException('文章不存在');
                }

                if (Article::where('id', '!=', $id)->whereTitle($validated['title'])->first()) {
                    throw new BusinessException('文章标题已存在');
                }
            } else {
                $article = new Article;

                if (Article::whereTitle($validated['title'])->first()) {
                    throw new BusinessException('文章标题已存在');
                }
            }
            DB::beginTransaction();

            try {
                $article->article_category_id = $validated['article_category_id'];
                $article->title = $validated['title'];
                $article->cover = $validated['cover'] ?? '';
                $article->keywords = $validated['keywords'] ?? '';
                $article->description = $validated['description'] ?? '';
                $article->is_top = intval($validated['is_top']);
                $article->is_login = intval($validated['is_login']);
                $article->is_show = intval($validated['is_show']);
                $article->is_recommend = intval($validated['is_recommend']);
                $article->author = $validated['author'] ?? '';
                $article->sort = $validated['sort'] ?? 0;
                $article->click_count = $validated['click_count'] ?? 0;
                $article->file_url = $validated['file_url'] ?? '';
                $article->goods_category_id = $validated['goods_category_id'] ?? 0;
                $article->admin_user_id = get_admin_user()->id;

                if (! $article->save()) {
                    throw new \Exception('保存文章失败');
                }

                // 文章内容处理
                $res = $article->articleContent()->updateOrCreate(['article_id' => $article->id], ['content' => app(HTMLPurifierService::class)->purify($validated['content'])]);

                if (! $res) {
                    throw new \Exception('保存文章内容失败');
                }

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('保存失败');
            }

            $log = "新增文章[id:{$article->id}]";

            if ($id) {
                $log = "编辑文章[id:{$article->id}]".implode(',', array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($article->getChanges()), $article->getChanges()));
            }
            admin_operation_log($log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('保存成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('文章操作异常~'.$throwable->getMessage());
        }
    }

    /**
     * 切换字段.
     */
    public function changeField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'value' => 'required|integer|in:0,1',
                'field' => 'required|string|in:is_show,is_top,is_login',
            ], [], [
                'id' => '文章ID',
                'value' => '值',
                'field' => '字段',
            ]);

            $article = Article::query()->whereId($validated['id'])->first();

            if (! $article) {
                throw new BusinessException('文章不存在');
            }
            $article->{$validated['field']} = $validated['value'];

            if (! $article->save()) {
                throw new BusinessException('切换失败');
            }

            $log = "更改文章字段[id:{$validated['id']}]".implode(
                ',',
                array_map(function ($k, $v) {
                    return sprintf('%s=`%s`', $k, $v);
                }, array_keys($article->getChanges()), $article->getChanges())
            );
            admin_operation_log($log, AdminOperationLog::TYPE_UPDATE);

            return $this->success('切换成功');
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('切换异常~');
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
                'id' => '文章ID',
            ]);
            $article = Article::query()->whereId($validated['id'])->first();

            if (! $article instanceof Article) {
                throw new BusinessException('文章不存在');
            }

            if ($article->delete()) {
                admin_operation_log("删除了文章:{$article->title}[{$article->id}]", AdminOperationLog::TYPE_DESTROY);

                return $this->success('删除成功');
            }

            throw new BusinessException('删除失败');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('删除异常~');
        }
    }

    /**
     * 生成副本.
     */
    public function copy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '文章ID',
            ]);
            $article = Article::query()->with(['articleContent'])->whereId($validated['id'])->first();

            if (! $article instanceof Article) {
                throw new BusinessException('文章不存在');
            }

            DB::beginTransaction();

            try {
                $article_info = $article->replicate();

                $new_title = $article_info->title.'-副本';
                $title_count = Article::whereTitle($new_title)->count();

                if ($title_count) {
                    $two_count = Article::where('title', 'like', '%'.$article_info->title.'-副本('.'%')->count();

                    if ($two_count) {
                        $new_title = $article_info->title.'-副本('.($two_count + 2).')';
                    } else {
                        $new_title = $article_info->title.'-副本('.($title_count + 1).')';
                    }
                }
                $article_info->is_show = Article::IS_SHOW_NO;
                $article_info->title = $new_title;
                $article_info->click_count = 0;
                $article_info->sort = 0;

                if (! $article_info->save()) {
                    throw new \Exception('生成文章失败');
                }

                // 保存文章内容
                $article_content = new ArticleContent;
                $article_content->content = $article->articleContent->content ?? '';
                $article_content->article_id = $article_info->id;

                if (! $article_content->save()) {
                    throw new \Exception('生成内容失败');
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                throw new BusinessException('生成副本失败');
            }

            admin_operation_log("生成副本文章:{$article_info->title}[{$article_info->id}]", AdminOperationLog::TYPE_STORE);

            return $this->success('生成副本成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('生成副本异常~');
        }
    }

    // 更新常用图
    public function updateCover(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'img_url' => 'required|string',
            ], [], [
                'id' => '图片ID',
                'img_url' => '图片地址',
            ]);

            $id = $validated['id'] ?? 0;
            $img_url = $validated['img_url'];

            if (! $id) {
                $article_cover = new ArticleCover;
            } else {
                $article_cover = ArticleCover::whereId($id)->first();
            }
            $article_cover->img_url = $img_url;

            if (! $article_cover->save()) {
                throw new BusinessException('更新常用图失败');
            }

            $data['id'] = $article_cover->id;
            $data['img_url'] = $article_cover->img_url;

            return $this->success($data);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('更新常用图异常~');
        }
    }

    // 删除常用图
    public function deleteCover(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '图片ID',
            ]);

            $article_cover = ArticleCover::whereId($validated['id'])->first();

            if (! $article_cover) {
                throw new BusinessException('常用图不存在');
            }

            if (! $article_cover->delete()) {
                throw new BusinessException('删除失败');
            }

            return $this->success('删除成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('删除常用图异常~');
        }
    }
}
