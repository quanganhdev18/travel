<?php

use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsGuide;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            SetLocale::class,
            CheckUserActive::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if (auth()->check()) {
                if (auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Staff'])) {
                    return route('admin.dashboard');
                }
                if (auth()->user()->hasRole('Guide')) {
                    return route('guide.dashboard');
                }
            }

            return '/';
        });

        $middleware->alias([
            'admin' => IsAdmin::class,
            'guide' => IsGuide::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
