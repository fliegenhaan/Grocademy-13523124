<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'instructor' => $this->faker->name,
            'topics' => $this->faker->words(3),
            'price' => $this->faker->numberBetween(50000, 300000),
            'thumbnail_image' => 'thumbnails/default.jpg',
        ];
    }
}