<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxHistory;

class TaxHistorySeeder extends Seeder
{
    public function run(): void
    {
        TaxHistory::factory()->count(20)->create();

        $this->command->info('Wygenerowano 20 przykładowych rekordów historii PIT.');
    }
}
