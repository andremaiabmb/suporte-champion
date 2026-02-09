<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rotas usam o middleware "api" e são automaticamente prefixadas com "api".
| Ex.: a rota abaixo responderá em GET /api/ping
*/

Route::get('/ping', function () {
    return response()->json([
        'pong' => true,
        'timestamp' => now()->toIso8601String(),
    ]);
});
