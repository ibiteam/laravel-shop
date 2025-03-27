<?php

use App\Enums\CustomCodeEnum;
use App\Exceptions\BusinessException;
use App\Http\Middleware\Api\Authenticate as ApiAuthenticate;
use App\Http\Middleware\Manage\Authenticate as ManageAuthenticate;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php'
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 重定向到首页
        $middleware->redirectGuestsTo('/');
        $middleware->group('api', [
            HandleCors::class,
        ]);

        $middleware->encryptCookies(except: ['appearance']);

        $middleware->alias([
            'manage.auth' => ManageAuthenticate::class,
            'api.auth' => ApiAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 不记录到日志的异常
        $exceptions->dontReport([
            BusinessException::class,
        ]);
        // 封装异常返回
        $exceptions->render(function (AuthenticationException $authentication_exception, $request) {
            // 如果不是json请求，则跳转登录
            if (! ($request->expectsJson() ?? false) && $redirect = $authentication_exception->redirectTo($request)) {
                return redirect()->to($redirect);
            }
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
