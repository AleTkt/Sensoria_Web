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
        // 1. Tu middleware global actual (Se ejecuta en todas las rutas web)
        $middleware->web(append: [
            \App\Http\Middleware\SetCurrentCompany::class,
        ]);

        // 2. Nuestro nuevo alias (Se ejecuta solo cuando ponemos middleware('role:...') en web.php)
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckCompanyRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
