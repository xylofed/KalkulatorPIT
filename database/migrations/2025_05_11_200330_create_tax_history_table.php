<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tax_calculation_id')
                ->constrained()
                ->onDelete('cascade'); // Powiązanie z kalkulacją podatkową

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // Powiązanie z użytkownikiem

            $table->string('action'); // Np. 'created', 'updated', 'deleted'

            $table->json('previous_values')->nullable(); // JSON poprzednich danych

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_history');
    }
};
