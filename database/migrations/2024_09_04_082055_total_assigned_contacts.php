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
        Schema::table('owners', function (Blueprint $table) {
            $table->integer('total_archive_contacts')->default(0)->after('total_assign_contacts');
            $table->integer('total_discard_contacts')->default(0)->after('total_archive_contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('total_archive_contacts');
            $table->dropColumn('total_discard_contacts');
        });
    }
};
