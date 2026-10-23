<?php

use App\Http\Middleware\CheckSsoApplicationAccess;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\ValidateOAuthClient;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'oauth.client' => ValidateOAuthClient::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);
        $middleware->web(
            append: [
                CheckSsoApplicationAccess::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
