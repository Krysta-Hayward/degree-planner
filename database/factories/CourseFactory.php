<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
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
            'courseNumber' => fake()->regexify('[A-Z]{3}[1-3]{3}'),
            'name' => fake()->sentence(2),
            'credits' => fake()->numberBetween(4,6),
        ];
    }
}
