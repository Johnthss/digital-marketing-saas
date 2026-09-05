<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\ContentAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContentAssetFactory extends Factory
{
    protected $model = ContentAsset::class;

    public function definition(): array
    {
        $types = ['document', 'image', 'video', 'audio', 'presentation', ''];
        $type = fake()->randomElement($types);
        $name = fake()->words(3, true);

        return [
            'agency_id' => Agency::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . uniqid(),
            'type' => $type,
            'description' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'file_path' => null,
            'file_url' => null,
            'file_size' => null,
            'file_mime' => null,
            'thumbnail_url' => null,
            'tags' => implode(', ', fake()->words(3)),
            'is_active' => true,
        ];
    }
}
