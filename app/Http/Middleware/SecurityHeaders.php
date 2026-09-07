<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking - only allow same origin framing
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // XSS Protection for older browsers
        $response->headers->set('X-XSS-Protection', '0');

        // Referrer Policy - don't leak URL to third parties
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - restrict browser features
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // Content Security Policy for HTML responses
        if ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.adminlte.io https://cdn.jsdelivr.net https://code.jquery.com; " .
                "style-src 'self' 'unsafe-inline' https://cdn.adminlte.io https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                "font-src 'self' https://cdn.adminlte.io https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
                "img-src 'self' data: https: blob:; " .
                "connect-src 'self' ws: wss:; " .
                "frame-ancestors 'self'; " .
                "base-uri 'self'; " .
                "form-action 'self'"
            );
        }

        return $response;
    }
}
