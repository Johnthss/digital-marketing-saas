<?php
namespace Database\Factories;
use App\Models\DataDeletionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataDeletionRequestFactory extends Factory
{
    protected $model = DataDeletionRequest::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'pending',
            'reason' => fake()->sentence(),
            'scheduled_at' => now()->addDays(30),
            'completed_at' => null,
        ];
    }
}
