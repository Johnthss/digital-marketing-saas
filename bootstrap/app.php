<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\HstsMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'agency' => \App\Http\Middleware\EnsureAgencyAccess::class,
            'feature' => \App\Http\Middleware\FeatureGate::class,
            'quota' => \App\Http\Middleware\EnforceQuota::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            HstsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            NotFoundHttpException::class,
            \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class,
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        ]);

        // API requests get JSON responses
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                    ? $e->getStatusCode()
                    : 500;

                $message = config('app.debug') ? $e->getMessage() : match ($statusCode) {
                    400 => 'Bad request.',
                    401 => 'Authentication required.',
                    403 => 'Access denied.',
                    404 => 'Resource not found.',
                    405 => 'Method not allowed.',
                    422 => 'Validation error.',
                    429 => 'Rate limit exceeded. Please try again later.',
                    500 => 'An unexpected error occurred. Please try again later.',
                    default => 'An error occurred.',
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'status' => $statusCode,
                    'errors' => $e instanceof ValidationException ? $e->errors() : null,
                ], $statusCode);
            }
        });
    })->create();
