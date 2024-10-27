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
        Schema::create('sale_agent', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name'); // Name of the sales agent
            $table->string('email')->unique(); // Unique email
            $table->string('hubspot_id')->nullable(); // Hubspot ID, nullable if not synced yet
            $table->string('nationality'); // Nationality of the agent
            $table->integer('total_hubspot_sync')->default(0); // New field: total HubSpot sync count
            $table->integer('total_in_progress')->default(0); // Total contacts in progress
            $table->integer('total_assign_contacts')->default(0); // Total assigned contacts
            $table->integer('total_archive_contacts')->default(0); // Total archived contacts
            $table->integer('total_discard_contacts')->default(0); // Total discarded contacts
            $table->foreignId('bu_country_id')->constrained('bu_country_buh'); // Foreign key to BU Country
            $table->timestamps(); // Laravel's created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_agent');
    }
};
