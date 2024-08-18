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
        Schema::create('owners', function (Blueprint $table) {
            $table->id('owner_pid');
            $table->string('owner_name');
            $table->string('owner_email_id');
            $table->string('owner_hubspot_id')->nullable();
            $table->string('owner_business_unit')->nullable();
            $table->string('country', 100);
            $table->integer('total_in_progress')->default(0);
            $table->integer('total_hubspot_sync')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
