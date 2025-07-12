<?php

// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware): void {

//         $middleware->append(\Illuminate\Cookie\Middleware\EncryptCookies::class);
//         $middleware->append(\Illuminate\Session\Middleware\StartSession::class);
//         $middleware->append(\Illuminate\View\Middleware\ShareErrorsFromSession::class);
//         $middleware->append(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
//         $middleware->append(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);

//         $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
//     })
//     ->withExceptions(function (Exceptions $exceptions): void {
//         //
//     })->create();


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Illuminate\Cookie\Middleware\EncryptCookies::class);
        $middleware->append(\Illuminate\Session\Middleware\StartSession::class);
        $middleware->append(\Illuminate\View\Middleware\ShareErrorsFromSession::class);
        // Don't add CSRF or Sanctum middleware yet - just get session working
    })
    ->create();
