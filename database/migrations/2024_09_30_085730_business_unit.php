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
        Schema::create('Business_Unit', function (Blueprint $table) {
            $table->id('bu_id'); // Primary key
            $table->string('business_unit'); // Business unit name
            $table->json('country'); // JSON field for countries
            $table->json('BUH'); // BUH field
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Business_Unit');
    }
};
