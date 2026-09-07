<?php

namespace Database\Factories;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFieldFactory extends Factory
{
    protected $model = CustomField::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'type' => fake()->randomElement(['text', 'number', 'date', 'select', 'checkbox']),
            'options' => null,
            'is_required' => false,
            'is_active' => true,
        ];
    }
}
