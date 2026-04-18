<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'waiter.linked' => \App\Http\Middleware\EnsureWaiterIsLinked::class,
            'order.portal' => \App\Http\Middleware\EnsureOrderPortalAuthenticated::class,
        ]);

        // Order Portal API (app nje ya browser) haitumii CSRF token
        $middleware->validateCsrfTokens(except: [
            'api/order-portal/*',
            'order-portal/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
