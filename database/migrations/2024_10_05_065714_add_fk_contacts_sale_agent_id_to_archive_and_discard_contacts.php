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
        // Add foreign key to archive_contacts table
        Schema::table('contact_archives', function (Blueprint $table) {
            $table->foreignId('fk_contacts__sale_agent_id')->nullable()->constrained('sale_agent')->onDelete('cascade');
        });

        // Add foreign key to discard_contacts table
        Schema::table('contact_discards', function (Blueprint $table) {
            $table->foreignId('fk_contacts__sale_agent_id')->nullable()->constrained('sale_agent')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key from archive_contacts table
        Schema::table('contact_archives', function (Blueprint $table) {
            $table->dropForeign(['fk_contacts__sale_agent_id']);
            $table->dropColumn('fk_contacts__sale_agent_id');
        });

        // Remove foreign key from discard_contacts table
        Schema::table('contact_discards', function (Blueprint $table) {
            $table->dropForeign(['fk_contacts__sale_agent_id']);
            $table->dropColumn('fk_contacts__sale_agent_id');
        });
    }
};
