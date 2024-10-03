<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\BusinessUnit;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define the country and BUH mappings
        $businessUnits = [
            [
                'business_unit' => 'SG Retail',
                'country' => json_encode(['Singapore']),
                'BUH' => json_encode([
                    'Singapore'=>'Max'
                ]),
            ],
            [
                'business_unit' => 'HED',
                'country' => json_encode(['Malaysia', 'Myanmar', 'Indonesia', 'Philippines', 'Vietnam', 'Cambodia', 'Laos', 'Thailand', 'India', 'Sri Lanka', 'Others']),
                'BUH' => json_encode([
                    'Malaysia' => 'She Nee',
                    'Myanmar' => 'Shine',
                    'Indonesia' => 'Tissa',
                    'Philippines' => 'Abbigail',
                    'Vietnam' => 'Dung',
                    'Cambodia' => 'Metta',
                    'Laos' => 'Metta',
                    'Thailand' => 'Metta',
                    'India' => 'Metta',
                    'Sri Lanka' => 'Rizvi',
                    'Others' => 'Metta',
                ])
            ],
            [
                'business_unit' => 'Alliance',
                'country' => json_encode(['Malaysia', 'Myanmar', 'Indonesia', 'Philippines', 'Vietnam', 'Cambodia', 'Laos', 'Thailand', 'India', 'Sri Lanka', 'Others']),
                'BUH' => json_encode([
                    'Malaysia' => 'Elise Tan',
                    'Myanmar' => 'Shine',
                    'Indonesia' => 'Indra',
                    'Philippines' => 'Hysie',
                    'Vietnam' => 'Dung',
                    'Cambodia' => 'Tep',
                    'Laos' => 'Tep',
                    'Thailand' => 'Tep',
                    'India' => 'Metta',
                    'Sri Lanka' => 'Rizvi',
                    'Others' => 'Metta',
                ])
            ],
            [
                'business_unit' => 'Enterprise International',
                'country' => json_encode(['Malaysia', 'Indonesia', 'Philippines']),
                'BUH' => json_encode([
                    'Malaysia' => 'Christopher',
                    'Indonesia' => 'Christopher',
                    'Philippines' => 'Christopher',
                ])
            ],
            [
                'business_unit' => 'Enterprise Singapore',
                'country' => json_encode(['Singapore']),
                'BUH' => json_encode([
                    'Singapore' => 'Caesar'
                ]),
            ],
            [
                'business_unit' => 'Talent Management',
                'country' => json_encode(['Malaysia', 'Singapore', 'Myanmar', 'Indonesia', 'Philippines', 'Vietnam', 'Cambodia', 'Laos', 'Thailand', 'India', 'Sri Lanka', 'Others']),
                'BUH' => json_encode([
                    'Malaysia' => 'Parvin',
                    'Singapore' => 'Parvin',
                    'Myanmar' => 'Parvin',
                    'Indonesia' => 'Parvin',
                    'Philippines' => 'Parvin',
                    'Vietnam' => 'Parvin',
                    'Cambodia' => 'Parvin',
                    'Laos' => 'Parvin',
                    'Thailand' => 'Parvin',
                    'India' => 'Parvin',
                    'Sri Lanka' => 'Parvin',
                    'Others' => 'Parvin',
                ])
            ],
        ];

        // Use Eloquent to insert the data into the Business_Unit table
        BusinessUnit::insert($businessUnits);
      
    }
}