<?php

namespace App\Http\Middleware\Api;

use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AccessLogService;
use App\Services\AccessLog\Factories\AccessLogInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = get_user();

        $formatter = new AccessLogFormatter;
        $formatter->setUserId($user->id ?? 0);
        $formatter->setUrl($request->fullUrl());
        $formatter->setMethod($request->getMethod());
        $formatter->setRefererUrl($request->server('HTTP_REFERER') ?? '');
        $formatter->setUserAgent($request->server('HTTP_USER_AGENT') ?? '');
        $formatter->setRequestData($request->all());

        $service = AccessLogService::init();

        if ($service instanceof AccessLogInterface) {
            $service->write($formatter);
        }

        return $next($request);
    }
}
