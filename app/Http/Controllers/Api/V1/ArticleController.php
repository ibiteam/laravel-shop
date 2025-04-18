<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\ArticleDao;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 文章.
 */
class ArticleController extends BaseController
{
    /**
     * 文章详情.
     */
    public function detail(Request $request, ArticleDao $article_dao)
    {
        $user = get_user();

        try {
            $validated = $request->validate([
                'article_id' => 'required|int',
            ], [], [
                'article_id' => '文章ID',
            ]);

            $article = $article_dao->getArticleById($validated['article_id'], $user);
            if (! $article) {
                throw new BusinessException('文章不存在');
            }

            return $this->success($article);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取文章详情异常~'.$throwable->getMessage());
        }
    }
}
