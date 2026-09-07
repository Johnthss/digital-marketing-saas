<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'subject' => fake()->sentence(),
            'html_content' => '<h1>Hello {{ name }}</h1>',
            'plain_text_content' => 'Hello {{ name }}',
            'category' => fake()->randomElement(['welcome', 'notification', 'marketing']),
            'variables' => ['name', 'email', 'agency_name'],
            'is_default' => false,
            'is_public' => false,
            'usage_count' => 0,
        ];
    }
}
