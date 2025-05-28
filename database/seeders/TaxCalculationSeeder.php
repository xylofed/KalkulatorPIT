<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxCalculation;

class TaxCalculationSeeder extends Seeder
{
    public function run(): void
    {
        // 5 publicznych, anonimowych kalkulacji
        TaxCalculation::factory()->count(5)->create([
            'user_id' => null,
            'is_public' => true,
        ]);
    }
}
