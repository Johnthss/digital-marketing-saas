<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

return new class extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'feature_key' => 'ai_assistant',
                'feature_name' => 'AI Assistant',
                'description' => 'Natural language AI assistant for agency management',
                'enabled' => true,
                'required_plan' => 1, // Starter plan+
                'allowed_roles' => ['owner', 'admin', 'manager'],
                'settings' => ['max_interactions_per_day' => 100],
            ],
            [
                'feature_key' => 'telegram_bot',
                'feature_name' => 'Telegram Bot',
                'description' => 'Telegram integration for agency commands',
                'enabled' => true,
                'required_plan' => 1,
                'allowed_roles' => ['owner', 'admin', 'manager'],
                'settings' => ['allowed_commands' => ['status', 'posts', 'analytics', 'ai']],
            ],
            [
                'feature_key' => 'workflow_engine',
                'feature_name' => 'Workflow Engine',
                'description' => 'Advanced workflow automation',
                'enabled' => false,
                'required_plan' => 2, // Pro plan+
                'allowed_roles' => ['owner', 'admin'],
                'minimum_version' => '1.1.0',
                'settings' => ['max_workflows' => 10],
            ],
            [
                'feature_key' => 'advanced_analytics',
                'feature_name' => 'Advanced Analytics',
                'description' => 'Predictive analytics and insights',
                'enabled' => false,
                'required_plan' => 2,
                'allowed_roles' => ['owner', 'admin', 'manager'],
                'minimum_version' => '1.2.0',
                'settings' => ['prediction_horizon_days' => 30],
            ],
        ];

        foreach ($features as $feature) {
            FeatureFlag::updateOrCreate(
                ['feature_key' => $feature['feature_key']],
                $feature
            );
        }
    }
};