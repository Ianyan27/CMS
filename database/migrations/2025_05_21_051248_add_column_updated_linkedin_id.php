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
        Schema::table('Hubspot_Contact_Profile', function (Blueprint $table) {
            $table->string('updated_linkedin_id')->nullable()->after('linkedin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Hubspot_Contact_Profile', function (Blueprint $table) {
            $table->dropColumn('updated_linkedin_id');
        });
    }
};
