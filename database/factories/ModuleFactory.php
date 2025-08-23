<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => 'Module: ' . $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'order' => $this->faker->unique()->numberBetween(1, 20),
            'pdf_content' => null,
            'video_content' => null,
        ];
    }
}