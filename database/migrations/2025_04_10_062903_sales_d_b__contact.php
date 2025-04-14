<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SalesDB_Contact_Profile', function (Blueprint $table) {
            $table->id('contact_id');
            $table->string('hubspot_id')->nullable(); //hubspot_id
            $table->string('contact_source')->nullable(); //ad_channel
            $table->string('contact_email')->nullable(); //email
            $table->string('contact_lastname')->nullable(); //lastname
            $table->string('contact_firstname')->nullable(); //firstname
            $table->string('contact_mobile')->nullable(); //Phone
            $table->string('linkedin_id')->nullable(); //hs_linkedin_url
            $table->string('facebook_id')->nullable(); // NA for the mean time
            $table->string('passport_full_name')->nullable(); //full_name_of_student__as_in_nric_
            $table->string('nric_id')->nullable(); //nric_number__for_sc_pr_
            $table->string('passport_id')->nullable(); //passport_number___fin__indicate_n_a_if_not_applicable___sgret_
            $table->string('date_of_birth')->nullable(); //age_sgret
            $table->string('race')->nullable(); //race
            $table->string('nationality')->nullable(); //nationality
            $table->string('parent_name')->nullable(); //parent_guardian_contact_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_
            $table->string('parent_email_id')->nullable(); //parent_guardian_email
            $table->string('parent_passport_id')->nullable(); //parent_guardian_nric_passport_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_
            $table->string('highest_qualification')->nullable(); //highest_level_of_education
            $table->string('qualifications_list')->nullable();
            $table->string('business_unit')->nullable(); //business_unit
            $table->string('academic_aptitude')->nullable();
            $table->string('career_segment')->nullable();
            $table->string('work_experience_yrs')->nullable(); //how_many_years_of_work_experience_do_you_have
            $table->string('current_company')->nullable(); //current_or_last_company
            $table->string('company_classification')->nullable(); //company_type
            $table->string('current_job_role')->nullable(); //jobtitle
            $table->string('job_classification')->nullable();
            $table->string('career_level')->nullable();
            $table->string('contact_cv')->nullable();
            $table->string('general_ksa_profile')->nullable();
            $table->string('digital_skills_profile')->nullable();
            $table->string('management_skills_profile')->nullable();
            $table->string('stem_skills')->nullable();
            $table->string('coding_skills')->nullable();
            $table->string('ai_skills')->nullable();
            $table->string('digital_marketing_skills')->nullable();
            $table->string('applications_skills')->nullable();
            $table->string('project_magt_skills')->nullable();
            $table->string('business_leader_skills')->nullable();
            $table->string('customer_magt_skills')->nullable();
            $table->string('data_skills')->nullable();
            $table->string('contact_persona')->nullable();
            $table->string('sales_affiliate')->nullable();
            
            // $table->string('sales_and_marketing')->nullable();
            // $table->string('business_operations')->nullable();
            // $table->string('finance_and_compliances')->nullable();
            // $table->string('hr_and_learning_development')->nullable();
            // $table->string('technology')->nullable();
            // $table->string('customer_magt_skills')->nullable();
            // $table->string('innovation_leadership_skills')->nullable();
            // $table->string('career_goal')->nullable();
            $table->timestamps();
            
        });

        Schema::create('SalesDB_Contact_Engagement_Status', function (Blueprint $table) {
            $table->id('contact_engagement_status_id');
            $table->unsignedBigInteger('contact_id');
            $table->string('contact_mgr')->nullable(); //account_manager__hed_
            $table->string('contact_exec')->nullable(); //hubspot_owner_id
            $table->string('contact_status')->nullable(); //contact_status
            $table->string('cilos_status')->nullable(); 
            $table->string('cilos_stage')->nullable(); //lifecyclestage
            $table->string('cilos_substage')->nullable(); //sales_lifecycle_l2
            $table->string('win_lost_reasons')->nullable();
            $table->string('proposed_solution')->nullable();
            $table->string('lead_status')->nullable();
            $table->string('product_interest')->nullable(); //which_course_are_you_interested_in
            $table->timestamps();

            $table->foreign('contact_id')->references('contact_id')->on('SalesDB_Contact_Profile');
        });

        Schema::create('SalesDB_Contact_Activities_Status', function (Blueprint $table) {
            $table->id('contact_activities_status_id');
            $table->unsignedBigInteger('contact_id');
            $table->dateTime('last_messaging_date')->nullable(); //notes_last_updated
            $table->text('last_messaging_contents')->nullable();
            $table->dateTime('last_campaign_date')->nullable();
            $table->text('last_campaign_contents')->nullable();
            $table->dateTime('last_digital_conversation_date')->nullable();
            $table->text('digital_conversation_contents')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('contact_id')->on('SalesDB_Contact_Profile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SalesDB_Contact_Activities_Status');
        Schema::dropIfExists('SalesDB_Contact_Engagement_Status');
        Schema::dropIfExists('SalesDB_Contact_Profile');
    }
};
