<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SalesDB_Company_Profiles', function (Blueprint $table) {
            $table->id();
            $table->string('businessunit')->nullable();
            $table->string('companyaddress')->nullable();
            $table->string('companyemail')->nullable();
            $table->string('companyname')->nullable();
            $table->string('companypersona')->nullable();
            $table->string('companypersona2')->nullable();
            $table->string('companyphone')->nullable();
            $table->text('companyprofiletext')->nullable();
            $table->string('companysource')->nullable();
            $table->string('companywebsite')->nullable();
            $table->string('facebookwebpage')->nullable();
            $table->string('industrysector')->nullable();
            $table->string('linkedinwebpage')->nullable();
            $table->uuid('salesdb_companyprofileid');
 
            $table->timestamps();
        });

        Schema::create('SalesDB_Company_Engagement_Statuses', function (Blueprint $table) {
            $table->id();
            $table->string('cilosstage')->nullable();
            $table->string('cilosstatus')->nullable();
            $table->string('cilossubstage')->nullable();
            $table->string('companyexec')->nullable();
            $table->string('companyid')->nullable();
            $table->string('companymgr')->nullable();
            $table->string('companyprospect')->nullable();
            $table->string('customerstatus')->nullable();
            $table->string('managedaccountyn')->nullable();
            $table->string('productinterest')->nullable();
            $table->text('proposedsolution')->nullable();
            $table->text('winlostreasons')->nullable();
            $table->uuid('salesdb_companyengagementstatusid');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SalesDB_Company_Profiles');
        Schema::dropIfExists('SalesDB_Company_Engagement_Statuses');
    }
};
