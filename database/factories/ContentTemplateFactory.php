<?php

namespace Database\Factories;

use App\Models\ContentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentTemplateFactory extends Factory
{
    protected $model = ContentTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'template_content' => fake()->paragraph(),
            'category' => fake()->randomElement(['email', 'social', 'blog', 'ad']),
            'is_active' => true,
        ];
    }
}
