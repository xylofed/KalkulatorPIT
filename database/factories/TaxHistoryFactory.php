<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\TaxCalculation;

class TaxHistoryFactory extends Factory
{
    public function definition(): array
    {
        $action = $this->faker->randomElement(['created', 'updated', 'deleted']);

        $values = [
            'income' => $income = $this->faker->numberBetween(50000, 150000),
            'expenses' => $expenses = $this->faker->numberBetween(10000, 30000),
            'deductions' => $deductions = $this->faker->numberBetween(0, 10000),
            'tax_type' => $taxType = $this->faker->randomElement(['scale', 'flat', 'ryczałt']),
            'children' => $this->faker->numberBetween(0, 10),
            'social_insurance' => $zus = $this->faker->numberBetween(5000, 15000),
            'health_insurance' => $health = $this->faker->numberBetween(3000, 9000),
            'is_married' => $this->faker->boolean(),
        ];

        $taxableIncome = max(0, $income - $expenses - $deductions - $zus - $health);
        $values['taxable_income'] = $taxableIncome;

        switch ($taxType) {
            case 'flat':
                $values['tax_amount'] = round($taxableIncome * 0.19, 2);
                break;
            case 'ryczałt':
                $values['tax_amount'] = round($income * 0.17, 2);
                break;
            default:
                $values['tax_amount'] = $taxableIncome < 120000
                    ? round($taxableIncome * 0.12, 2)
                    : round(14400 + ($taxableIncome - 120000) * 0.32, 2);
        }

        // Przygotowanie danych do kolumny previous/new values
        $previous = null;
        $new = null;

        if ($action === 'created') {
            $new = $values;
        } elseif ($action === 'updated') {
            $previous = $values;
            // symulujemy zmiany – np. dochód lub dzieci się zmieniły
            $changed = $values;
            $changed['income'] += $this->faker->numberBetween(1000, 10000);
            $changed['children'] += $this->faker->numberBetween(0, 2);
            $taxableIncome = max(0, $changed['income'] - $changed['expenses'] - $changed['deductions'] - $changed['social_insurance'] - $changed['health_insurance']);
            $changed['taxable_income'] = $taxableIncome;

            switch ($changed['tax_type']) {
                case 'flat':
                    $changed['tax_amount'] = round($taxableIncome * 0.19, 2);
                    break;
                case 'ryczałt':
                    $changed['tax_amount'] = round($changed['income'] * 0.17, 2);
                    break;
                default:
                    $changed['tax_amount'] = $taxableIncome < 120000
                        ? round($taxableIncome * 0.12, 2)
                        : round(14400 + ($taxableIncome - 120000) * 0.32, 2);
            }

            $new = $changed;
        } elseif ($action === 'deleted') {
            $previous = $values;
        }

        return [
            'tax_calculation_id' => TaxCalculation::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'action' => $action,
            'previous_values' => $previous ? json_encode($previous) : null,
            'new_values' => $new ? json_encode($new) : null,
        ];
    }
}
