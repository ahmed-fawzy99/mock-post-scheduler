<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'image_url' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'SCHEDULED']),
        ];
        if ($data['status'] === 'SCHEDULED') {
            $data['scheduled_at'] = $this->faker->dateTimeBetween('now', '+1 year');
        }
        return $data;
    }
}
