<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('income_sources', function (Blueprint $table) {
            $table->id();
            $table->string('source_name'); // nazwa źródła dochodu
            $table->decimal('amount', 12, 2); // kwota dochodu
            $table->integer('tax_year'); // rok podatkowy
            $table->unsignedBigInteger('user_id'); // powiązanie z użytkownikiem
            $table->timestamps();

            // Klucz obcy, powiązanie z tabelą users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_sources');
    }
};
