<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\AiContentLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiContentLogFactory extends Factory
{
    protected $model = AiContentLog::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'content_type' => fake()->randomElement(['post', 'caption', 'hashtag', 'headline', 'email', 'ad_copy']),
            'action_type' => fake()->randomElement(['generate', 'rewrite', 'summarize', 'translate', 'ideate']),
            'prompt' => fake()->sentence(),
            'output' => fake()->paragraphs(2, true),
            'provider' => fake()->randomElement(['openai', 'anthropic', 'google']),
            'model' => fake()->randomElement(['gpt-4o', 'gpt-3.5-turbo', 'claude-3-opus', 'gemini-pro']),
            'tokens_used' => fake()->numberBetween(50, 2000),
            'cost' => fake()->randomFloat(4, 0.001, 0.05),
            'metadata' => null,
        ];
    }
}
