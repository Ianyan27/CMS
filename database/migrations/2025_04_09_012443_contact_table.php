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
        Schema::create('Hubspot_Contact_Profile', function (Blueprint $table) {
            $table->id('contact_id');
            $table->string('hubspot_id')->unique(); //hubspot_id 
            $table->text('contact_source')->nullable(); //ad_channel
            $table->text('contact_email')->nullable(); //email
            $table->text('contact_lastname')->nullable(); //lastname
            $table->text('contact_firstname')->nullable(); //firstname
            $table->text('contact_mobile')->nullable(); //Phone
            $table->text('linkedin_id')->nullable(); //hs_linkedin_url
            $table->text('facebook_id')->nullable(); // NA for the mean time
            $table->text('passport_full_name')->nullable(); //full_name_of_student__as_in_nric_
            $table->text('nric_id')->nullable(); //nric_number__for_sc_pr_
            $table->text('passport_id')->nullable(); //passport_number___fin__indicate_n_a_if_not_applicable___sgret_
            $table->text('date_of_birth')->nullable(); //age_sgret
            $table->text('race')->nullable(); //race
            $table->text('nationality')->nullable(); //nationality
            $table->text('parent_name')->nullable(); //parent_guardian_contact_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_
            $table->text('parent_email_id')->nullable(); //parent_guardian_email
            $table->text('parent_passport_id')->nullable(); //parent_guardian_nric_passport_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_
            $table->text('highest_qualification')->nullable(); //highest_level_of_education
            $table->text('qualifications_list')->nullable();
            $table->text('business_unit')->nullable(); //business_unit
            $table->text('academic_aptitude')->nullable();
            $table->text('career_segment')->nullable();
            $table->text('work_experience_yrs')->nullable(); //how_many_years_of_work_experience_do_you_have
            $table->text('current_company')->nullable(); //current_or_last_company
            $table->text('company_classification')->nullable(); //company_type
            $table->text('current_job_role')->nullable(); //jobtitle
            $table->text('job_classification')->nullable();
            $table->text('career_level')->nullable();
            $table->text('contact_cv')->nullable();
            $table->text('general_ksa_profile')->nullable();
            $table->text('digital_skills_profile')->nullable();
            $table->text('management_skills_profile')->nullable();
            $table->text('stem_skills')->nullable();
            $table->text('coding_skills')->nullable();
            $table->text('ai_skills')->nullable();
            $table->text('digital_marketing_skills')->nullable();
            $table->text('applications_skills')->nullable();
            $table->text('project_magt_skills')->nullable();
            $table->text('business_leader_skills')->nullable();
            $table->text('customer_magt_skills')->nullable();
            $table->text('contact_persona')->nullable();
            $table->text('sales_affiliate')->nullable();

            //Contact Engagement Status
            $table->text('contact_mgr')->nullable(); //account_manager__hed_
            $table->text('contact_exec')->nullable(); //hubspot_owner_id
            $table->text('managed_contact_yn')->nullable();
            $table->text('contact_status')->nullable(); //contact_status
            $table->text('cilos_status')->nullable(); 
            $table->text('cilos_stage')->nullable(); //lifecyclestage
            $table->text('cilos_substage')->nullable(); //sales_lifecycle_l2
            $table->text('win_lost_reasons')->nullable();
            $table->text('proposed_solution')->nullable();
            $table->text('lead_status')->nullable(); //lead_status
            $table->text('product_interest')->nullable(); //which_course_are_you_interested_in
            //Contact Acitivities Status
            $table->text('last_messaging_date')->nullable(); //notes_last_updated
            $table->text('last_messaging_contents')->nullable();
            $table->text('last_campaign_date')->nullable();
            $table->text('last_campaign_contents')->nullable();
            $table->text('last_digital_conversation_date')->nullable();
            $table->text('digital_conversation_contents')->nullable();
            $table->text('campaign_engagement_contents')->nullable();
            $table->text('messaging_engagement_score')->nullable();
            $table->text('messaging_sentiment_score')->nullable();
            $table->text('conversation_engagement_score')->nullable();
            $table->text('leads_score')->nullable();
            $table->text('leads_score_summary')->nullable();

            $table->integer('temp_id')->nullable(); //temp_id
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Hubspot_Contact_Profile');
    }
};
