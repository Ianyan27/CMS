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
        Schema::table('bu_country_buh', function (Blueprint $table) {
            $table->foreignId('bu_id')->constrained('bu')->nullable(); // Adding FK to BU table
            $table->foreignId('country_id')->constrained('country')->nullable(); // Adding FK to Country table
            $table->foreignId('buh_id')->constrained('buh'); // Adding FK to BUH table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bu_country', function (Blueprint $table) {
            $table->dropForeign(['bu_id']);
            $table->dropColumn('bu_id');
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
            $table->dropForeign(['buh_id']);
            $table->dropColumn('buh_id');
        });
    }
};
