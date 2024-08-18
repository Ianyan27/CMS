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
        // database/migrations/xxxx_xx_xx_xxxxxx_create_engagement_discards_table.php


        Schema::create('engagement_discards', function (Blueprint $table) {
            $table->id('engagement_discard_pid');
            $table->foreignId('fk_engagement_discards__contact_discard_pid')
                ->constrained('contact_discards', 'contact_discard_pid')
                ->onDelete('cascade')
                ->name('fk_engagement_contact_discard');  // Shortened name for the foreign key
            $table->string('activity_name', 100);
            $table->date('date');
            $table->text('details')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagement_discards');
    }
};
