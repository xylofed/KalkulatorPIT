<?php

namespace Database\Factories;

use App\Models\TaxCalculation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxCalculationFactory extends Factory
{
    protected $model = TaxCalculation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'income' => $this->faker->randomFloat(2, 50000, 200000),
            'expenses' => $this->faker->randomFloat(2, 10000, 50000),
            'deductions' => $this->faker->randomFloat(2, 0, 10000),
            'tax_type' => $this->faker->randomElement(['scale', 'flat', 'ryczałt']),
            'children' => $this->faker->numberBetween(0, 3),
            'is_married' => $this->faker->boolean(),
            'social_insurance' => $this->faker->randomFloat(2, 5000, 15000),
            'health_insurance' => $this->faker->randomFloat(2, 3000, 8000),
            'taxable_income' => $this->faker->randomFloat(2, 30000, 150000),
            'tax_amount' => $this->faker->randomFloat(2, 3000, 25000),
        ];
    }
}
