<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies — app runs behind nginx reverse proxy
        $middleware->trustProxies(at: '*');

        // Aliases for route middleware
        $middleware->alias([
            'shift.open'   => \App\Http\Middleware\EnsureShiftIsOpen::class,
            'role'         => \App\Http\Middleware\CheckRole::class,
            'allow.iframe' => \App\Http\Middleware\AllowIframeForDemo::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);

        // Unauthenticated users go to POS login page
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
