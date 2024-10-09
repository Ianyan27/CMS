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
        Schema::table('moved_contacts', function (Blueprint $table) {
            Schema::table('moved_contacts', function (Blueprint $table) {
                // Add the new column for the foreign key
                $table->foreignId('fk_contacts__sale_agent_id')->nullable()->constrained('sale_agent')->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moved_contacts', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['fk_contacts__sale_agent_id']);
            $table->dropColumn('fk_contacts__sale_agent_id');
        });
    }
};
