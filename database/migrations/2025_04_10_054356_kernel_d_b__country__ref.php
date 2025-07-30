<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('KernelDB_Country_Ref', function (Blueprint $table) {
            $table->id('country_id');
            $table->string('country_code');
            $table->string('country_desc');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('KernelDB_Country_Ref');
    }
};
