<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_code' => 'PT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(1, 90),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];
    }
}
