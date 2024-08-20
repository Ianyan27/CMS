<?php
namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsImport implements ToModel, WithHeadingRow
{
    protected $columnMap = [
        'name' => ['name', 'full_name', 'contact_name', 'lead_full_name', 'Lead Full Name'],
        'email' => ['email', 'email_address', 'contact_email'],
        'contact_number' => ['contact_number', 'phone_number', 'mobile', 'phone', 'contact_no', 'Contact No'],
        'address' => ['address', 'contact_address', 'location', 'lead_location_raw'],
        'country' => ['country', 'nation', 'lead_location_country_name'],
        'qualification' => ['qualification', 'degree', 'education'],
        'job_role' => ['job_role', 'position', 'role', 'lead_job_title'],
        'company_name' => ['company_name', 'company', 'organization'],
        'skills' => ['skills', 'expertise', 'competencies'],
        'social_profile' => ['social_profile', 'linkedin', 'twitter', 'lead_linkedin_url'],
        'status' => ['status'],
        'source' => ['source', 'origin', 'referral_source'],
        'datetime_of_hubspot_sync' => ['datetime_of_hubspot_sync', 'sync_datetime', 'hubspot_sync_date'],
    ];

    public function getColumnMap()
    {
        return $this->columnMap;
    }

    public function model(array $row)
    {
        $data = [];
        $normalizedRow = array_change_key_case($row, CASE_LOWER);

        foreach ($this->columnMap as $field => $possibleColumns) {
            foreach ($possibleColumns as $column) {
                $normalizedColumn = strtolower(trim($column));
                if (isset($normalizedRow[$normalizedColumn])) {
                    $data[$field] = trim($normalizedRow[$normalizedColumn]);
                    break;
                }
            }
        }

        return new Contact($data);
    }
}
