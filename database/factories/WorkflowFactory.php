<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'agency_id' => Agency::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . uniqid(),
            'description' => fake()->sentence(),
            'trigger_type' => fake()->randomElement(['comment_received', 'mention', 'new_follower', 'scheduled', 'post_published']),
            'actions' => [
                ['type' => 'auto_reply', 'config' => ['message' => 'Thank you for your message!']],
            ],
            'is_active' => true,
            'last_run_at' => null,
            'run_count' => 0,
        ];
    }
}
