<?php

namespace Database\Factories;

use App\Models\DataExportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataExportRequestFactory extends Factory
{
    protected $model = DataExportRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'pending',
            'export_types' => ['posts', 'campaigns', 'clients'],
            'file_path' => null,
            'completed_at' => null,
        ];
    }
}
