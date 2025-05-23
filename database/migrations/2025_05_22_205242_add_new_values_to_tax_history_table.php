<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewValuesToTaxHistoryTable extends Migration
{
    public function up()
    {
        Schema::table('tax_history', function (Blueprint $table) {
            $table->json('new_values')->nullable()->after('previous_values');
        });
    }

    public function down()
    {
        Schema::table('tax_history', function (Blueprint $table) {
            $table->dropColumn('new_values');
        });
    }
}
