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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            // NOTE: CheckAdmin is intentionally NOT global.
            // It is applied per route group via middleware('role:admin') in routes/web.php.
            // Applying it globally would block /login, /register, and all student/professor routes.
        ]);
        $middleware->alias([
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
            'check.admin' => \App\Http\Middleware\CheckAdmin::class,
            'protect.sensitive' => \App\Http\Middleware\ProtectSensitiveRoutes::class,
            'check.contract' => \App\Http\Middleware\CheckProfessorContract::class,
            'admin.2fa'  => \App\Http\Middleware\AdminTwoFactorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
