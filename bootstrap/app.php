<?php

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
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if (auth()->check()) {
                if (in_array(auth()->user()->role, ['admin', 'staff'])) {
                    return route('admin.dashboard');
                }
                if (auth()->user()->role === 'guide') {
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
