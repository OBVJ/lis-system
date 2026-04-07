<?php

namespace Database\Factories;

use App\Models\TestCategory;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'category_id' => TestCategory::factory(),
            'normal_min' => $this->faker->randomFloat(2, 0, 50),
            'normal_max' => $this->faker->randomFloat(2, 51, 100),
            'unit' => $this->faker->randomElement(['mg/dL', 'g/dL', 'mmol/L', 'U/L']),
            'price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
