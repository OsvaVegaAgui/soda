<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Cargado manualmente porque en este punto el .env aun no lo carga el framework.
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('usuarios', ['accion' => 'login']));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// En hosting con el public real fuera de app_soda (ver .env de produccion: APP_PUBLIC_PATH).
if ($publicPath = env('APP_PUBLIC_PATH')) {
    $app->usePublicPath($publicPath);
}

return $app;
