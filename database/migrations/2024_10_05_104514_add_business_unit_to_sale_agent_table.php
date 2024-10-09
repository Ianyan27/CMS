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
        // Modify the sale_agent table to add the business_unit column
        Schema::table('sale_agent', function (Blueprint $table) {
            $table->string('business_unit')->nullable()->after('nationality'); // Add business_unit column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_agent', function (Blueprint $table) {
            $table->dropColumn('business_unit');
        });
    }
};
