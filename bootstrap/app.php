<?php

use App\Http\Middleware\EnforceQuota;
use App\Http\Middleware\EnsureAgencyAccess;
use App\Http\Middleware\FeatureGate;
use App\Http\Middleware\HstsMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'agency' => EnsureAgencyAccess::class,
            'feature' => FeatureGate::class,
            'quota' => EnforceQuota::class,
            'agent.rate_limit' => \App\Http\Middleware\AgentRateLimit::class,
        ]);

        $middleware->web(append: [
            SecurityHeaders::class,
            AddLinkHeadersForPreloadedAssets::class,
            HstsMiddleware::class,
        ]);

        $middleware->api(append: [
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            NotFoundHttpException::class,
            AccessDeniedHttpException::class,
            MethodNotAllowedHttpException::class,
        ]);

        // API requests get JSON responses
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = match (true) {
                    $e instanceof HttpException => $e->getStatusCode(),
                    $e instanceof ValidationException => 422,
                    $e instanceof AuthenticationException => 401,
                    $e instanceof AuthorizationException => 403,
                    default => 500,
                };

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
