<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactProfile extends Model
{
    protected $table = 'SalesDB_Contact_Profile';
    protected $primaryKey = 'contact_id';
    public $timestamps = true;

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
    ];

    public function engagementStatuses() {
        return $this->hasMany(ContactEngagementStatus::class, 'contact_id');
    }

    public function activityStatuses() {
        return $this->hasMany(ContactActivitiesStatus::class, 'contact_id');
    }
}
