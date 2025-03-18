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
            $table->string('phone')->nullable();
            $table->string('hubspot_owner_id')->nullable();
            $table->string('hs_lead_status')->nullable();
            $table->string('company')->nullable();
            $table->string('lifecyclestage')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubspot_contacts', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'hubspot_owner_id',
                'hs_lead_status',
                'company',
                'lifecyclestage',
                'country',
            ]);
        });
    }
};
