<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('tax_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('income', 15, 2);
            $table->decimal('expenses', 15, 2);
            $table->decimal('deductions', 15, 2);
            $table->string('tax_type');
            $table->integer('children')->default(0);
            $table->decimal('social_insurance', 15, 2)->default(0);
            $table->decimal('health_insurance', 15, 2)->default(0);
            $table->boolean('is_married')->default(false);
            $table->decimal('taxable_income', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->timestamps();

            // Indeks na user_id (opcjonalnie, przyspiesza zapytania)
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_calculations');
    }
}
