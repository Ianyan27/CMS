<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('engagement_archives', function (Blueprint $table) {
            $table->id('engagement_archive_pid');
            $table->foreignId('fk_engagement_archives__contact_archive_pid')
                ->constrained('contact_archives', 'contact_archive_pid')
                ->onDelete('cascade')
                ->name('fk_engagement_contact_archive');  // Shortened name for the foreign key
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
        Schema::dropIfExists('engagement_archives');
    }
};
