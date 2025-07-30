<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('KernelDB_BU_Ref', function (Blueprint $table) {
            $table->id('bu_id');
            $table->string('bu_code');
            $table->string('bu_desc');
            $table->unsignedBigInteger('country_id');
            $table->string('region');
            $table->timestamps();

            $table->foreign('country_id')->references('country_id')->on('KernelDB_Country_Ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('KernelDB_BU_Ref');
    }
};
