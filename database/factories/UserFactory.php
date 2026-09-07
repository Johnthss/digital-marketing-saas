<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test'.uniqid().'@example.com',
            'email_verified_at' => null,
            'password' => Hash::make('password123'),
            'agency_id' => null,
            'role' => 'owner',
            'avatar' => null,
            'title' => 'Owner',
            'phone' => null,
            'last_active_at' => null,
            'is_active' => true,
            'is_approved' => true,
            'notes' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function manager(): static
    {
        return $this->state(['role' => 'manager']);
    }

    public function member(): static
    {
        return $this->state(['role' => 'member']);
    }
}
