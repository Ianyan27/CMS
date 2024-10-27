<?php
// Need to be implement by Kyaw Naing Win still not finished
namespace App\Http\Controllers;

use App\Models\BuCountryBUH;
use App\Models\Country;
use App\Models\User; // Make sure to import the User model
use App\Models\Contact; // Import the Contact model
use App\Models\Engagement; // Import the Engagement model
use App\Models\EngagementArchive; // Import the EngagementArchive model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\BU;
use App\Models\BuCountry;
use App\Models\BUH;
use App\Models\SaleAgent;

class HeadController extends Controller
{
    // Display a listing of users
    // Controller method for fetching BUHs assigned to a head
    public function index()
    {
        $headId = Auth::id(); // Get the authenticated user's ID

        $userData = DB::table('bu_country_buh as bc')
            ->join('bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country', 'bc.country_id', '=', 'country.id')
            ->join('buh', 'bc.buh_id', '=', 'buh.id')
            ->where('buh.head_id', $headId) // Filter by head_id
            ->select(
                'bc.id as id',
                'bu.name as bu_name',
                'country.name as country_name',
                'buh.name as buh_name',
                'buh.email as buh_email',
                'buh.nationality'
            )
            ->paginate(10);

        $currentPage = $userData->currentPage();
        $perPage = $userData->perPage();
        $businessUnit = BU::all();
        $countries = Country::all();

        // $dropdownData = DB::table('Business_Unit')->select('business_unit','country')->get();
        // Log::info('dropdown Data are - '.$dropdownData);

        // Return the data to the view
        return view('Head_Page', [
            'userData' => $userData,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'businessUnit' => $businessUnit,
            'countries' => $countries

        ]);
    }

    public function viewUser()
{
    $userData = DB::table('bu_country_buh as bc')
        ->join('bu', 'bc.bu_id', '=', 'bu.id')
        ->join('country', 'bc.country_id', '=', 'country.id')
        ->join('buh', 'bc.buh_id', '=', 'buh.id')
        ->select(
            'bc.id as id',
            'bu.name as bu_name',
            'country.name as country_name',
            'buh.name as buh_name',
            'buh.email as buh_email',
            'buh.nationality'
        )
        ->paginate(10);

    // Pass the results to the view
    return view('Head_page', [
        'userData' => $userData,
        'businessUnit' => BU::with('countries')->get()
    ]);
}

    // Save a new user
    public function saveUser(Request $request)
    {
        $headId = Auth::id();
        $domainRegex = 'lithan.com|educlaas.com|learning.educlaas.com';
    
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users', // Ensures the email is unique in the users table
                function ($attribute, $value, $fail) use ($domainRegex) {
                    if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                        $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                    }
                }
            ],
            'nationality' => 'required|string|max:255',
            'business_unit' => 'required|string',
            'country' => 'required|array|min:1',
        ]);
    
        // Start a database transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // Check if the user already exists
            $existingUser = User::where('email', $validatedData['email'])->first();
            if ($existingUser) {
                return redirect()->back()->withErrors(['email' => 'User with this email already exists.']);
            }
    
            // Create the user only once
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->role = $request->input('role');
            $user->password = bcrypt('default');
            $user->save();
    
            // Create the BUH entry only once
            $buh = new BUH();
            $buh->name = $validatedData['name'];
            $buh->email = $validatedData['email'];
            $buh->nationality = $validatedData['nationality'];
            $buh->head_id = $headId;
            $buh->save();
    
            // Fetch the business unit
            $bu = BU::where('name', $validatedData['business_unit'])->first();
            if (!$bu) {
                DB::rollBack();
                return redirect()->back()->withErrors(['business_unit' => 'Business Unit not found.']);
            }
    
            // Get country IDs for the provided country names
            $countryIds = Country::whereIn('name', $validatedData['country'])->pluck('id');
    
            // Link BUH to multiple countries in the `bu_country_buh` table
            foreach ($countryIds as $countryId) {
                $buCountryBUH = new BuCountryBUH();
                $buCountryBUH->bu_id = $bu->id;
                $buCountryBUH->country_id = $countryId;
                $buCountryBUH->buh_id = $buh->id;
                $buCountryBUH->save();
            }
    
            // Commit the transaction
            DB::commit();
            return redirect()->back()->with('success', 'User added successfully!');
            
        } catch (\Exception $e) {
            // Rollback transaction if there is an error
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred while creating the user.']);
        }
    }
    


    // Edit user details
    public function editUser($id)
    {
        $userData = DB::table('bu_country as bc')
            ->join('bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country', 'bc.country_id', '=', 'country.id')
            ->join('buh', 'bc.buh_id', '=', 'buh.id')
            ->select('bc.id as id', 'bu.name as bu_name', 'country.name as country_name', 'buh.name as buh_name', 'buh.email as buh_email', 'buh.nationality')
            ->where('bc.id', $id)
            ->first();

        $countries = DB::table('country')->get();
        $businessUnits = DB::table('bu')->get();

        return view('edit-user', [
            'users' => $userData,
            'countries' => $countries,
            'businessUnits' => $businessUnits
        ]);
    }
    public function updateBUH(Request $request, $id)
    {
        try {
            // Fetch the correct buh_id using the $id from bu_country
            $buCountry = DB::table('bu_country')->where('id', $id)->first();
            $buhId = $buCountry->buh_id;

            // Fetch the correct user record based on the buh_id
            $buh = DB::table('buh')->where('id', $buhId)->first();
            $user = DB::table('users')->where('email', $buh->email)->first(); // Use email to fetch the corresponding user

            // Ensure nationality is provided, if not, set a default or handle it properly
            $nationality = $request->input('nationality', 'Not Provided'); // Default value 'Not Provided' if nationality is missing

            // Check if the user exists before updating
            if ($user) {
                // Update the user in the users table
                DB::table('users')->where('id', $user->id)->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 'BUH',
                    'updated_at' => now(),
                ]);
            } else {
                Log::error('User not found for BUH email: ' . $buh->email);
            }

            // Update the BUH's information in the buh table, but keep the head_id intact
            DB::table('buh')->where('id', $buhId)->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'nationality' => $nationality,
                'updated_at' => now(),
            ]);

            // Retrieve the ID from the bu table based on the business unit name
            $buId = DB::table('bu')
                ->where('name', $request->input('business_unit'))
                ->value('id');

            // Retrieve the ID from the country table based on the country name
            $countryId = DB::table('country')
                ->where('name', $request->input('country'))
                ->value('id');

            // Update the bu_country table without affecting the business unit table
            DB::table('bu_country')->where('id', $id)->update([
                'bu_id' => $buId,
                'country_id' => $countryId,
                'updated_at' => now(),
            ]);

            Log::info('User and BUH update completed successfully.');

            return redirect()->back()->with('success', 'BUH updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }



    // Delete a user// Delete a user
    public function deleteUser($id)
    {
        DB::beginTransaction(); // Start a database transaction

        try {
            // Fetch the related buh_id and email using the provided id from bu_country
            $buCountry = DB::table('bu_country as bc')
                ->join('buh as b', 'bc.buh_id', '=', 'b.id')
                ->select('bc.buh_id', 'b.email') // Select the email from the buh table
                ->where('bc.id', $id)
                ->first();

            if (!$buCountry) {
                return redirect()->route('head.view-user')->with('error', 'User not found.');
            }

            $buhId = $buCountry->buh_id;

            // Delete the entry in the bu_country table first
            DB::table('bu_country')->where('id', $id)->delete();

            // Then delete the entry in the buh table
            DB::table('buh')->where('id', $buhId)->delete();

            // Finally, delete the entry in the users table using the retrieved email
            $user = DB::table('users')->where('email', $buCountry->email)->first();
            if ($user) {
                DB::table('users')->where('id', $user->id)->delete();
            } else {
                Log::warning('User not found for deletion with email: ' . $buCountry->email);
            }

            DB::commit(); // Commit the transaction

            // return redirect()->route('head.view-user')->with('success', 'User deleted successfully!');
            return redirect()->back()->with('success', 'BUH deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete BUH.');
        }
    }

    public function getBUData(Request $request)
    {
        // Validate that the business_unit is provided as a string
        $request->validate([
            'business_unit' => 'required|string',
        ]);

        // Get the name of the Business Unit from the request
        $buName = $request->get('business_unit');

        // Find the Business Unit by its name
        $bu = BU::where('name', $buName)->first();

        // If no BU is found, return an error response
        if (!$bu) {
            return response()->json(['error' => 'Business Unit not found'], 404);
        }

        // Get the corresponding BU ID
        $buId = $bu->id;

        // Assuming the BU model has a 'country' relationship
        $countryName = $bu->country; // Access country name directly from BU

        // Find the BUH records related to this BU
        $buhList = BUH::whereHas('buCountries', function ($query) use ($buId) {
            $query->where('bu_id', $buId);
        })->get();

        // Prepare the response including the country name and BUHs
        $response = [
            'country' => $countryName,
            'buh' => $buhList->pluck('name'), // Get the names of the BUHs related to this BU
        ];

        // Return the response as JSON
        return response()->json($response);
    }


    public function getBUHByBusinessUnit(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'business_unit' => 'required|string',
        ]);

        // Get business unit from request
        $businessUnitName = $request->input('business_unit');

        // Find the corresponding BU
        $bu = BU::where('name', $businessUnitName)->first();
        if (!$bu) {
            return response()->json(['error' => 'Business Unit not found'], 404);
        }

        // Retrieve BUHs associated with this BU
        $buhList = BUH::whereHas('buCountries', function ($query) use ($bu) {
            $query->where('bu_id', $bu->id);
        })->get();

        // Check if any BUHs were found
        if ($buhList->isEmpty()) {
            return response()->json(['error' => 'No BUH found for the specified business unit'], 404);
        }

        // Return the list of BUHs as JSON
        return response()->json(['buh' => $buhList]);
    }

    public function viewBUHDetails($id)
    {

        $buhData = DB::table('bu_country_buh as bc')
            ->join('bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country', 'bc.country_id', '=', 'country.id')
            ->join('buh', 'bc.buh_id', '=', 'buh.id')
            ->select(
                'bc.id as id',
                'bu.name as bu_name',
                'country.name as country_name',
                'buh.name as buh_name',
                'buh.email as buh_email',
                'buh.nationality as buh_nationality'
            )
            ->where('bc.id', $id)
            ->first();
        $saleAgents = SaleAgent::where('bu_country_id', $id)->get();
        $totalSaleAgents = $saleAgents->count();
        $totalDisabledSaleAgents = SaleAgent::where('bu_country_id', $id)
            ->where('status', 'inactive')->count();

        $buhSaleAgents = SaleAgent::where('bu_country_id', $id)->paginate(10);

        $businessUnit = BU::all();
        $countries = Country::all();

        return view('Edit_BUH_Detail_Page', [
            'buhSaleAgents' => $buhSaleAgents,
            'buhData' => $buhData,
            'totalSaleAgents' => $totalSaleAgents,
            'totalDisabledSaleAgents' => $totalDisabledSaleAgents,
            'businessUnit' => $businessUnit,
            'countries' => $countries
        ]);
    }

    // View contact details
    public function viewContact($contact_pid) {}
}
