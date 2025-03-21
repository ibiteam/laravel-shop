<?php

use App\Enums\CustomCodeEnum;
use App\Exceptions\BusinessException;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\Manage\Authenticate as ManageAuthenticate;
use App\Http\Middleware\Manage\CustomStartSession;
use App\Traits\ApiResponse;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            HandleCors::class,
        ]);

        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'manage.auth' => ManageAuthenticate::class,
        ]);
        $middleware->group('manage', array_values(array_filter([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            CustomStartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            SubstituteBindings::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ])));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 不记录到日志的异常
        $exceptions->dontReport([
            BusinessException::class,
        ]);
        // 封装异常返回
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $authentication_exception) {
            $api_response = new class
            {
                use ApiResponse;
            };

            return $api_response->error('未登录，请先登录。', CustomCodeEnum::UNAUTHORIZED);
        })->render(function (UnauthorizedHttpException $exception) {
            $api_response = new class
            {
                use ApiResponse;
            };

            return $api_response->error('请重新登录', CustomCodeEnum::UNAUTHORIZED);
        })->render(function (ValidationException $validation_exception) {
            $api_response = new class
            {
                use ApiResponse;
            };

            return $api_response->error($validation_exception->validator->errors()->first());
        })->render(function (UnauthorizedException $exception) {
            $api_response = new class
            {
                use ApiResponse;
            };

            return $api_response->error($exception->getMessage(), CustomCodeEnum::UNAUTHORIZED);
        });
    })->create();
