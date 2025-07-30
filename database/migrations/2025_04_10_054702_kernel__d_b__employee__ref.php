<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('KernelDB_Employee_Ref', function (Blueprint $table) {
            $table->id('employee_id');
            $table->unsignedBigInteger('owner_id');
            $table->string('employee_name');
            $table->string('supervisor');
            $table->unsignedBigInteger('bu_id');
            $table->timestamps();

            $table->foreign('bu_id')->references('bu_id')->on('KernelDB_BU_Ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('KernelDB_Employee_Ref');
    }
};
