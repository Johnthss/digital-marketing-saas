<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureGate
{
    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        $user = $request->user();

        if (! $user || ! $user->agency) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }
            abort(403);
        }

        $agency = $user->agency;

        foreach ($features as $feature) {
            if (! $agency->isFeatureAvailable($feature)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => "Feature '{$feature}' is not available on your plan.",
                        'upgrade_url' => route('agency.settings'),
                    ], 403);
                }
                abort(403, "Feature '{$feature}' is not available on your current plan.");
            }
        }

        return $next($request);
    }
}
