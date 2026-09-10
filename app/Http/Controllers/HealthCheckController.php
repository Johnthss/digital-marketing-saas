<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Basic health check - always returns 200 if the app is running.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'DigitalMarketingSaaS',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Readiness check - verifies all dependencies are working.
     */
    public function readiness(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'ai_gateway' => $this->checkAiGateway(),
        ];

        $healthy = !in_array(false, $checks, true);

        return response()->json([
            'status' => $healthy ? 'ready' : 'not_ready',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    /**
     * Liveness check - verifies the application is alive.
     */
    public function liveness(): JsonResponse
    {
        return response()->json([
            'status' => 'alive',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Detailed system status for monitoring.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'environment' => config('app.env'),
            'debug' => config('app.debug'),
            'version' => config('app.version', '1.0.0'),
            'checks' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'storage' => $this->checkStorage(),
                'ai_gateway' => $this->checkAiGateway(),
            ],
            'stats' => [
                'agencies' => DB::table('agencies')->count(),
                'users' => DB::table('users')->count(),
                'posts' => DB::table('social_posts')->count(),
                'campaigns' => DB::table('campaigns')->count(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            Cache::put('health_check', true, 10);
            return Cache::get('health_check') === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            return Storage::disk('local')->put('health_check.txt', 'ok');
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkAiGateway(): bool
    {
        try {
            $gateway = app(\App\Services\AI\Gateway\AiGateway::class);
            return $gateway->hasAvailableProvider();
        } catch (\Exception $e) {
            return false;
        }
    }
}
