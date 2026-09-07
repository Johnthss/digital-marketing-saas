<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Create features
        $features = [
            'analytics' => 'Analytics Dashboard',
            'scheduling' => 'Content Scheduling',
            'content_library' => 'Content Library',
            'campaign_manager' => 'Campaign Manager',
            'client_portal' => 'Client Portal',
            'performance_predictor' => 'AI Performance Prediction',
            'workflow_engine' => 'Workflow Automation',
            'social_inbox' => 'Social Inbox',
            'custom_branding' => 'Custom Branding',
            'api_access' => 'API Access',
            'email_marketing' => 'Email Marketing',
            'landing_pages' => 'Landing Pages',
            'form_builder' => 'Form Builder',
            'priority_support' => 'Priority Support',
            'dedicated_account_manager' => 'Dedicated Account Manager',
        ];

        foreach ($features as $code => $name) {
            Feature::firstOrCreate(['code' => $code], [
                'name' => $name,
                'description' => "Access to {$name} feature.",
                'is_active' => true,
            ]);
        }

        // Create plans
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'interval' => 'month',
                'users' => 1,
                'social_accounts' => 1,
                'posts_per_month' => 30,
                'campaigns' => 1,
                'clients' => 0,
                'ai_requests_per_month' => 50,
                'ai_generations_per_month' => 20,
                'landing_pages' => 0,
                'forms' => 0,
                'features' => [],
                'description' => 'Perfect for individuals getting started with social media management.',
                'is_active' => true,
                'is_visible' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 29,
                'interval' => 'month',
                'users' => 3,
                'social_accounts' => 3,
                'posts_per_month' => 100,
                'campaigns' => 3,
                'clients' => 5,
                'ai_requests_per_month' => 200,
                'ai_generations_per_month' => 100,
                'landing_pages' => 2,
                'forms' => 2,
                'features' => ['analytics', 'scheduling', 'content_library'],
                'description' => 'Great for small teams and growing agencies.',
                'is_active' => true,
                'is_visible' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 79,
                'interval' => 'month',
                'users' => 10,
                'social_accounts' => 10,
                'posts_per_month' => 500,
                'campaigns' => 10,
                'clients' => 25,
                'ai_requests_per_month' => 1000,
                'ai_generations_per_month' => 500,
                'landing_pages' => 10,
                'forms' => 10,
                'features' => ['analytics', 'scheduling', 'content_library', 'campaign_manager', 'client_portal', 'analytics_dashboard', 'performance_predictor'],
                'description' => 'Best for established agencies with multiple clients.',
                'is_active' => true,
                'is_visible' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 199,
                'interval' => 'month',
                'users' => -1,
                'social_accounts' => -1,
                'posts_per_month' => -1,
                'campaigns' => -1,
                'clients' => -1,
                'ai_requests_per_month' => -1,
                'ai_generations_per_month' => -1,
                'landing_pages' => -1,
                'forms' => -1,
                'features' => ['analytics', 'scheduling', 'content_library', 'campaign_manager', 'client_portal', 'analytics_dashboard', 'performance_predictor', 'workflow_engine', 'social_inbox', 'custom_branding', 'api_access', 'priority_support', 'dedicated_account_manager'],
                'description' => 'Unlimited everything for large agencies.',
                'is_active' => true,
                'is_visible' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::firstOrCreate(['slug' => $planData['slug']], $planData);
        }
    }
}
