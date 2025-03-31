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
        Schema::create('hubspot_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('hubspot_id')->unique()->index()->nullable();
            $table->string('email')->nullable()->index();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('gender')->nullable();
            $table->timestamp('hubspot_created_at')->nullable();
            $table->timestamp('hubspot_updated_at')->nullable();
            $table->timestamps();
        });

                // Create a table to track sync status and progress
                Schema::create('hubspot_sync_status', function (Blueprint $table) {
                    $table->id();
                    $table->string('entity_type');
                    $table->timestamp('last_sync_timestamp')->nullable();
                    $table->timestamp('next_sync_timestamp')->nullable();
                    $table->timestamp('last_successful_sync')->nullable();
                    $table->timestamp('start_window')->nullable();
                    $table->timestamp('end_window')->nullable();
                    $table->integer('total_synced')->default(0);
                    $table->integer('total_errors')->default(0);
                    $table->text('error_log')->nullable();
                    $table->string('status')->default('idle');
                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_contacts');
        Schema::dropIfExists('hubspot_sync_status');
    }
};
