<?php

namespace App\Http\Middleware\Manage;

use App\Enums\ConstantEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\AccessRecordDao;
use App\Models\AdminUser;
use App\Services\AccessLog\AccessLogService;
use App\Services\AccessLog\AdminAccessLogFormatter;
use App\Services\AccessLog\Factories\AccessLogInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessRecord
{
    /**
     * @throws BusinessException
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $admin_user = get_admin_user();

        if (! ($admin_user instanceof AdminUser)) {
            throw new BusinessException('用户未登录或用户异常', ConstantEnum::UNAUTHORIZED);
        }

        /* 分渠道记录访问日志 */
        $formatter = new AdminAccessLogFormatter;

        $referer_url = $request->server('HTTP_REFERER') ?? '';

        if (strlen($referer_url) > 240) {
            $referer_url = substr($referer_url, 240);
        }
        $formatter->setAdminUserId($admin_user->id ?? 0);
        $formatter->setUrl($request->fullUrl());
        $formatter->setMethod($request->getMethod());
        $formatter->setRefererUrl($referer_url);
        $formatter->setUserAgent($request->server('HTTP_USER_AGENT') ?? '');
        $formatter->setRequestData($request->all());

        $service = AccessLogService::init();

        if ($service instanceof AccessLogInterface) {
            $service->manageWrite($formatter);
        }

        $route_name = $request->route()->getName();

        app(AccessRecordDao::class)->updateOrCreate($admin_user->id, $route_name);

        return $next($request);
    }
}
