<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;

class ContactProfileExport implements FromQuery, WithMapping, WithChunkReading, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'contact_id',
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
            'business_unit',
            'academic_aptitude',
            'career_segment',
            'work_experience_yrs',
            'current_company',
            'company_classification',
            'current_job_role',
            'job_classification',
            'career_level',
            'contact_cv',
            'general_ksa_profile',
            'digital_skills_profile',
            'management_skills_profile',
            'stem_skills',
            'coding_skills',
            'ai_skills',
            'digital_marketing_skills',
            'applications_skills',
            'project_magt_skills',
            'business_leader_skills',
            'customer_magt_skills',
            'contact_persona',
            'sales_affiliate',
            'contact_mgr',
            'contact_exec',
            'managed_contact_yn',
            'contact_status',
            'cilos_status',
            'cilos_stage',
            'cilos_substage',
            'win_lost_reasons',
            'proposed_solution',
            'product_interest',
            'last_messaging_date',
            'last_messaging_contents',
            'last_campaign_date',
            'last_campaign_contents',
            'last_digital_conversation_date',
            'digital_conversation_contents',
            'campaign_engagement_contents',
            'messaging_engagement_score',
            'messaging_sentiment_score',
            'conversation_engagement_score',
            'leads_score',
            'leads_score_summary'
        ];
    }

    public function map($row): array
    {
        return [
            $row->contact_id,
            $row->hubspot_id,
            $row->contact_source,
            $row->contact_email,
            $row->contact_lastname,
            $row->contact_firstname,
            $row->contact_mobile,
            $row->linkedin_id,
            $row->facebook_id,
            $row->passport_full_name,
            $row->nric_id,
            $row->passport_id,
            $row->date_of_birth,
            $row->race,
            $row->nationality,
            $row->parent_name,
            $row->parent_email_id,
            $row->parent_passport_id,
            $row->highest_qualification,
            $row->qualifications_list,
            $row->business_unit,
            $row->academic_aptitude,
            $row->career_segment,
            $row->work_experience_yrs,
            $row->current_company,
            $row->company_classification,
            $row->current_job_role,
            $row->job_classification,
            $row->career_level,
            $row->contact_cv,
            $row->general_ksa_profile,
            $row->digital_skills_profile,
            $row->management_skills_profile,
            $row->stem_skills,
            $row->coding_skills,
            $row->ai_skills,
            $row->digital_marketing_skills,
            $row->applications_skills,
            $row->project_magt_skills,
            $row->business_leader_skills,
            $row->customer_magt_skills,
            $row->contact_persona,
            $row->sales_affiliate,
            $row->contact_mgr,
            $row->contact_exec,
            $row->managed_contact_yn,
            $row->contact_status,
            $row->cilos_status,
            $row->cilos_stage,
            $row->cilos_substage,
            $row->win_lost_reasons,
            $row->proposed_solution,
            $row->product_interest,
            $row->last_messaging_date,
            $row->last_messaging_contents,
            $row->last_campaign_date,
            $row->last_campaign_contents,
            $row->last_digital_conversation_date,
            $row->digital_conversation_contents,
            $row->campaign_engagement_contents,
            $row->messaging_engagement_score,
            $row->messaging_sentiment_score,
            $row->conversation_engagement_score,
            $row->leads_score,
            $row->leads_score_summary
        ];
    }

    public function chunkSize(): int
    {
        return 500; // Lower = less memory usage, increase if performance is good
    }

    public function query()
    {
        // $rows = DB::table('Hubspot_Contact_Profile')
        //     ->where('contact_id', '>=', 103513)
        //     ->where('contact_id', '<', 130426)
        //     ->count();

        // dd($rows);

        // return DB::table('Hubspot_Contact_Profile')
        //     ->where('temp_id', '>=', 19551)
        //     ->where('temp_id', '<', 41617)
        //     ->orderBy('contact_id', 'asc');
        return DB::table('Hubspot_Contact_Profile')
            ->where('temp_id', '=', null)
            ->orderBy('contact_id', 'asc');
    }
}
