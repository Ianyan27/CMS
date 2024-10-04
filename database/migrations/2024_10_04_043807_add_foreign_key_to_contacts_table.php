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
        Schema::table('contacts', function (Blueprint $table) {
            // Add the foreign key column first
            if (!Schema::hasColumn('contacts', 'fk_contacts__sale_agent_id')) {
                $table->unsignedBigInteger('fk_contacts__sale_agent_id')->nullable();

                // Add the foreign key constraint
                $table->foreign('fk_contacts__sale_agent_id')
                    ->references('id')
                    ->on('sale_agent') // Ensure the table name is correct
                    ->onDelete('cascade'); // Optional: cascade deletes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['fk_contacts__sale_agent_id']);

            // Drop the column if rolling back
            $table->dropColumn('fk_contacts__sale_agent_id');
        });
    }
};
