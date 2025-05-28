<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdzenie, czy użytkownik o emailu 'admin@example.com' już istnieje
        if (!User::where('email', 'admin@example.com')->exists()) {

            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]);
            echo "Administrator został dodany.\n";
        } else {
            echo "Użytkownik o emailu admin@example.com już istnieje.\n";
        }

        // Sprawdzenie, czy użytkownik o emailu 'user@example.com' już istnieje
        if (!User::where('email', 'user@example.com')->exists()) {
            // Tworzenie użytkownika testowego
            User::create([
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
            echo "Użytkownik testowy został dodany.\n";
        } else {
            echo "Użytkownik o emailu user@example.com już istnieje.\n";
        }
    }
}
