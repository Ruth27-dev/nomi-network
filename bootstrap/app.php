<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['Admin'])
                ->prefix('admin')
                ->name('admin-')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'    => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin.guard'           => \App\Http\Middleware\AdminGuard::class,
            'api_web.guard'         => \App\Http\Middleware\ApiWebGuard::class,
            'locale'                => \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->group('Admin', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->view('admin::pages.no-permission', [], 403);
        });
        //handle page not found
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->view('admin::errors.404', [], 404);
        });
    })
    ->create();
