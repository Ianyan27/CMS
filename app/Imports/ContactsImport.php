<?php
namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

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
        'datetime_of_hubspot_sync' => ['datetime_of_hubspot_sync', 'sync_datetime', 'hubspot_sync_date'],
    ];

    private $validRows = [];
    private $invalidRows = [];
    private $duplicateRows = [];
    private $duplicateCount = 0;

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

        // Check for duplicates
        if (Contact::where('email', $data['email'])->exists()) {
            $this->duplicateRows[] = $row;
            $this->duplicateCount++;
            return null; // Do not process this record further
        }

        // Validate the data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:contacts,email',
            'contact_number' => 'string|max:20',
        ]);

        if ($validator->fails()) {
            $this->invalidRows[] = array_merge($row, ['validation_errors' => $validator->errors()->all()]);
            return null;
        }

        $this->validRows[] = $data;
        return new Contact($data);
    }

    public function getValidCount()
    {
        return count($this->validRows);
    }

    public function getInvalidCount()
    {
        return count($this->invalidRows);
    }

    public function getDuplicateCount()
    {
        return $this->duplicateCount;
    }

    public function getInvalidRows()
    {
        return $this->invalidRows;
    }

    public function getDuplicateRows()
    {
        return $this->duplicateRows;
    }
}