<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFlagFactory extends Factory
{
    protected $model = FeatureFlag::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'key' => fake()->slug(),
            'description' => fake()->sentence(),
            'is_enabled' => true,
            'metadata' => null,
        ];
    }
}
