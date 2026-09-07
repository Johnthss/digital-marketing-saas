<?php

namespace Database\Factories;

use App\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkflowTemplateFactory extends Factory
{
    protected $model = WorkflowTemplate::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . uniqid(),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['social', 'content', 'engagement', 'analytics', 'automation']),
            'icon' => fake()->randomElement(['fa-project-diagram', 'fa-robot', 'fa-bell', 'fa-reply', 'fa-globe']),
            'nodes' => [
                ['id' => 1, 'type' => 'trigger', 'subtype' => 'post_published', 'x' => 100, 'y' => 200, 'config' => [], 'label' => 'Post Published'],
                ['id' => 2, 'type' => 'action', 'subtype' => 'send_notification', 'x' => 350, 'y' => 200, 'config' => ['message' => 'New post published!'], 'label' => 'Send Notification'],
            ],
            'connections' => [['from' => 1, 'to' => 2]],
            'is_public' => true,
            'is_active' => true,
            'usage_count' => fake()->numberBetween(0, 100),
        ];
    }
}
