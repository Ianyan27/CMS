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
        Schema::create('bu_country', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('bu_id')->constrained('bu')->nullable(); // Adding FK to BU table
            $table->foreignId('country_id')->constrained('country')->nullable(); // Adding FK to Country table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bu_countries');
    }
};
