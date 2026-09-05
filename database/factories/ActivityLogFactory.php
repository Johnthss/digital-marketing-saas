<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'action_type' => fake()->randomElement(['created', 'updated', 'deleted', 'published', 'scheduled']),
            'model_type' => null,
            'model_id' => null,
            'metadata' => null,
        ];
    }
}
