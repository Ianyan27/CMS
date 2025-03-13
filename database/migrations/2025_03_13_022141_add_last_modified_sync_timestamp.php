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
        Schema::table('hubspot_sync_status', function (Blueprint $table) {
            $table->timestamp('last_modified_sync_timestamp')->nullable()->after('last_sync_timestamp');
        });

        Schema::table('hubspot_retrieval_histories', function (Blueprint $table) {
            $table->string('sync_type')->default('create')->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubspot_sync_status', function (Blueprint $table) {
            $table->dropColumn('last_modified_sync_timestamp');
        });

        Schema::table('hubspot_retrieval_histories', function (Blueprint $table) {
            $table->dropColumn('sync_type');
        });
    }
};
