<?php

namespace App\Services;

use App\Models\Agency;

class FeatureFlagService
{
    public function isEnabled(Agency $agency, string $featureCode): bool
    {
        // Enterprise plan always has all features
        if ($agency->subscription_plan === 'enterprise') {
            return true;
        }

        $plan = $agency->getPlanConfig();
        $features = $plan['features'] ?? [];

        return in_array($featureCode, $features, true);
    }

    public function getEnabledFeatures(Agency $agency): array
    {
        if ($agency->subscription_plan === 'enterprise') {
            return config('platform.features') ?? [];
        }

        $plan = $agency->getPlanConfig();

        return $plan['features'] ?? [];
    }
}
