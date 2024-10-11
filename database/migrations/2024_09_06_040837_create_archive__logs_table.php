<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archive__logs', function (Blueprint $table) {
        
            $table->id('archive_log_pid');
            $table->foreignId('fk_logs__archive_contact_pid')->nullable()->constrained('contact_archives','contact_archive_pid');
            $table->foreignId('fk_logs__owner_pid')->nullable()->constrained('sale_agent','id');
            $table->enum('action_type', ['Allocated', 'Accessed', 'Updated', 'Converted to Archive', 'Converted to Discard', 'Deleted', 'Save New Activity']);
            $table->text('action_description')->nullable();
            $table->dateTime('action_timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('allocation_date')->nullable();
            $table->dateTime('access_date')->nullable();
            $table->dateTime('activity_datetime')->nullable();
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive__logs');
    }
};
