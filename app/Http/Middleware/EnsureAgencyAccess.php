<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgencyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->agency_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No agency assigned.'], 403);
            }
            abort(403, 'No agency assigned to your account.');
        }

        $agency = $user->agency;

        if (!$agency || $agency->status === 'cancelled') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Agency not active.'], 403);
            }
            abort(403, 'Your agency account is not active.');
        }

        // Share agency with all views
        view()->share('currentAgency', $agency);

        return $next($request);
    }
}
