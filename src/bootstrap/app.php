<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use App\Support\ApiResponse;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Database Query Exception
        $exceptions->render(function (QueryException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('DATABASE_ERROR', [
                    'exception' => $e->getMessage(),
                ], 400);
            }
        });

        // Validation Exception
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    'VALIDATION_ERROR',
                    $e->errors(),
                    422
                );
            }
        });

        // General Exception
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('SERVER_ERROR', [
                    'exception' => $e->getMessage(),
                ], 500);
            }
        });
    })->create();
