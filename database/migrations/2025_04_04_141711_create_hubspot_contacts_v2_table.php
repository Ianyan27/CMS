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
        Schema::create('hubspot_contacts_v2', function (Blueprint $table) {
            $table->id();
            $table->string('hubspot_id')->unique(); // Still required/unique
            $table->string('country')->nullable();
            $table->string('country_from')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('ad_channel')->nullable();
            $table->string('your_specialization')->nullable();
            $table->string('campaign_group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_contacts_v2');
    }
};
