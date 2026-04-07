<?php

namespace Database\Factories;

use App\Models\LabTest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabTest>
 */
class LabTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'category_id' => \App\Models\TestCategory::factory(),
            'normal_min' => $this->faker->numberBetween(0, 50),
            'normal_max' => $this->faker->numberBetween(60, 150),
            'unit' => $this->faker->randomElement(['mg/dL', 'g/dL', '%', '×10³/μL']),
            'price' => $this->faker->numberBetween(20, 200),
        ];
    }
}
