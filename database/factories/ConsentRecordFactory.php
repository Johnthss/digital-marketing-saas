<?php

namespace Database\Factories;

use App\Models\ConsentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentRecordFactory extends Factory
{
    protected $model = ConsentRecord::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'consent_type' => fake()->randomElement(['marketing', 'analytics', 'third_party']),
            'granted' => fake()->boolean(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
