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
        Schema::table('hubspot_contacts', function (Blueprint $table) {
            $table->string('marked_deleted')->default('no')->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubspot_contacts', function (Blueprint $table) {
            $table->dropColumn('marked_deleted');
        });
    }
};
