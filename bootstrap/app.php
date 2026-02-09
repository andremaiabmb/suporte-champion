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
        /**
         * Aqui registramos aliases de middleware.
         * Ex.: 'role' => \App\Http\Middleware\RoleMiddleware::class
         * Assim você pode usar nas rotas: ->middleware('role:admin|professor')
         * \App\Http\Middleware\SetPreferredLang::class,
         */
        $middleware->web(append: [
        \App\Http\Middleware\SetPreferredLang::class,
    ]);
        \App\Http\Middleware\SetPreferredLang::class;
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        /**
         * Se precisar anexar middlewares em grupos:
         * $middleware->appendToGroup('web', [ /* ... * / ]);
         * $middleware->appendToGroup('api', [ /* ... * / ]);
         */
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
