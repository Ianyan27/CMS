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
        Schema::table('sale_agent', function (Blueprint $table) {
            Schema::table('sale_agent', function (Blueprint $table) {
                $table->string('status')->default('active'); // Add status column with default value 'active'
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_agent', function (Blueprint $table) {
            $table->dropColumn('status'); // Remove the status column if rolled back
        });
    }
};
