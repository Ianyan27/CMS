<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactsImport implements ToModel, WithHeadingRow
{
    protected $columnMap = [
        'name' => ['name', 'full_name', 'contact_name', 'lead_full_name', 'Lead Full Name'],
        'first_name' => ['first_name', 'first', 'fname'],
        'last_name' => ['last_name', 'last', 'lname'],
        'email' => ['email', 'Email', 'email_address', 'contact_email'],
        'contact_number' => ['contact_number', 'phone_number', 'mobile', 'phone', 'contact_no', 'Contact No', 'contact_number_include_country_code'],
        'address' => ['address', 'contact_address', 'location', 'lead_location_raw'],
        'country' => ['country', 'nation', 'lead_location_country_name', 'countryregion'],
        'qualification' => ['qualification', 'degree', 'education'],
        'job_role' => ['job_role', 'position', 'role', 'lead_job_title', 'Job Title', 'job_title'],
        'company_name' => ['company_name', 'company', 'organization'],
        'skills' => ['skills', 'expertise', 'competencies'],
        'social_profile' => ['social_profile', 'linkedin', 'twitter', 'lead_linkedin_url'],
        'datetime_of_hubspot_sync' => ['datetime_of_hubspot_sync', 'sync_datetime', 'hubspot_sync_date'],
    ];

    private $validRows = [];
    private $invalidRows = [];
    private $duplicateRows = [];
    private $unselectedCountry = [];
    private $duplicateCount = 0;

    protected $platform;  // Add platform property
    protected $country;
    // Constructor to accept platform
    public function __construct($platform,  $country)
    {
        $this->platform = $platform;
        $this->country = $country;
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

        Log::info('selected country in before validate ' . $this->country);

        // Combine first_name and last_name into name
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        }

        // Ensure that required fields like 'email' and 'name' are present in the data array
        if (empty($data['email'])) {
            $this->invalidRows[] = array_merge($row, ['validation_errors' => ['Email field is missing or not recognized']]);
            return null; // Skip this row
        }

        if (empty($data['name'])) {
            $this->invalidRows[] = array_merge($row, ['validation_errors' => ['Name field is missing or not recognized']]);
            return null; // Skip this row
        }
        // Check if the country matches the selected country
        if (isset($data['country']) && strtolower($data['country']) !== strtolower($this->country)) {
            $this->unselectedCountry[] = array_merge($row, ['validation_errors' => ['Country does not match the selected country']]);
            return null; // Move to unselectedCountry array instead of invalidRows
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
            'email' => 'email|max:255|unique:contacts,email|required_without:contact_number',
            'contact_number' => 'string|max:20|required_without:email',
        ]);

        if ($validator->fails()) {
            $this->invalidRows[] = array_merge($row, ['validation_errors' => $validator->errors()->all()]);
            return null;
        }

        $data['source'] = $this->platform; // Add platform to data

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

    public function getUnselectedCountryCount()
    {
        return count($this->unselectedCountry);
    }

    public function getInvalidRows()
    {
        return $this->invalidRows;
    }

    public function getDuplicateRows()
    {
        return $this->duplicateRows;
    }


    public function getUnselectedCountryRows()
    {
        return $this->unselectedCountry;
    }
}
