<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\LandingPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandingPageFactory extends Factory
{
    protected $model = LandingPage::class;

    public function definition(): array
    {
        $statuses = ['draft', 'published', 'archived'];

        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'headline' => fake()->sentence(6),
            'subheadline' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'cta_text' => 'Get Started',
            'cta_url' => fake()->url(),
            'background_color' => '#ffffff',
            'text_color' => '#333333',
            'button_color' => '#007bff',
            'button_text_color' => '#ffffff',
            'views_count' => fake()->numberBetween(0, 1000),
            'conversions_count' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement($statuses),
            'published_at' => now(),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
