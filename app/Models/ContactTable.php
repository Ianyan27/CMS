<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTable extends Model
{
    protected $table = 'Hubspot_Contact_Profile';
    protected $primaryKey = 'contact_id';

    protected $fillable = [
        'hubspot_id',
        'contact_source', 
        'contact_email', 
        'contact_lastname', 
        'contact_firstname', 
        'contact_mobile', 
        'linkedin_id', 
        'facebook_id',
        'passport_full_name', 
        'nric_id', 
        'passport_id', 
        'date_of_birth', 
        'race', 
        'nationality',
        'parent_name', 
        'parent_email_id', 
        'parent_passport_id', 
        'highest_qualification',
        'qualifications_list', 
        'business_unit', 'academic_aptitude', 'career_segment',
        'work_experience_yrs', 'current_company', 'company_classification', 'current_job_role',
        'job_classification', 'career_level', 'contact_cv', 'general_ksa_profile',
        'digital_skills_profile', 'management_skills_profile', 'stem_skills', 'coding_skills',
        'ai_skills', 'digital_marketing_skills', 'applications_skills', 'project_magt_skills',
        'business_leader_skills', 'customer_magt_skills', 'contact_persona','sales_affiliate', 
        'contact_mgr',
        'contact_exec',
        'contact_status',
        'cilos_status',
        'cilos_stage',
        'cilos_substage',
        'win_lost_reasons',
        'lead_status',
        'proposed_solution',
        'product_interest',
        'contact_id',
        'lead_status',
        'last_messaging_date', 
        'last_messaging_contents', 
        'last_campaign_date', 
        'contact_id',
        'last_campaign_contents', 
        'last_digital_conversation_date', 
        'digital_conversation_contents',
        'campaign_engagement_score', 
        'messaging_engagement_score', 
        'messaging_sentiment_score',
        'conversation_engagement_score', 
        'leads_score', 
        'leads_score_summary',
        'notes_last_updated'
    ];

    public function engagementStatuses() {
        return $this->hasMany(ContactEngagementStatus::class, 'contact_id');
    }
}
