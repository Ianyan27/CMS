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
        Schema::create('transfer_contacts', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('contact_number', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('qualification')->nullable();
            $table->string('job_role')->nullable();
            $table->string('company_name')->nullable();
            $table->string('skills')->nullable();
            $table->string('social_profile')->nullable();
            $table->enum('status', ['New', 'InProgress', 'HubSpot Contact', 'Archive', 'Discard']);
            $table->string('source')->nullable();
            $table->dateTime('datetime_of_hubspot_sync')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_contacts');
    }
};
