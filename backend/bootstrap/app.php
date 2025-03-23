<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/apiVersions/v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            \Illuminate\Support\Facades\Log::info($e->getMessage());
            $statusCode = 500;
            switch (get_class($e)) {
                case \Illuminate\Validation\ValidationException::class:
                    $statusCode = 422;
                    break;
                case \Illuminate\Auth\AuthenticationException::class:
                    $statusCode = 401;
                    break;
                case \Illuminate\Auth\Access\AuthorizationException::class:
                    $statusCode = 403;
                    break;
                case \Illuminate\Database\Eloquent\ModelNotFoundException::class:
                    $statusCode = 404;
                    break;
            }
            return response()->json([
                'status' => $statusCode,
                'message' => $e->getMessage(),
                'errors' => method_exists($e, 'errors') ? $e->errors() : [],
            ], $statusCode);
        });
    })->create();
