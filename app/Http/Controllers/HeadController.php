<?php
// Need to be implement by Kyaw Naing Win still not finished
namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use App\Models\Contact; // Import the Contact model
use App\Models\Engagement; // Import the Engagement model
use App\Models\EngagementArchive; // Import the EngagementArchive model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Db;

class HeadController extends Controller
{
    // Display a listing of users    // Display a listing of users
 // In your Laravel controller
public function index()
{
    $userData = DB::table('bu_country as bc')
        ->join('bu', 'bc.bu_id', '=', 'bu.id')
        ->join('country', 'bc.country_id', '=', 'country.id')
        ->join('buh', 'bc.buh_id', '=', 'buh.id')
        ->select(
            'bc.id as id', 
            'bu.name as bu_name', 
            'country.name as country_name', 
            'buh.name as buh_name', 
            'buh.email as buh_email',
            'buh.nationality' // Include nationality here
        )
        ->paginate(10);

    // Pass the current page and per page values to the view
    $currentPage = $userData->currentPage();
    $perPage = $userData->perPage();

    // Pass the results to the view
    return view('Head_page', [
        'userData' => $userData,
        'currentPage' => $currentPage,
        'perPage' => $perPage,
        'countries' => DB::table('country')->get(),
        'businessUnits' => DB::table('bu')->get()
    ]);
}
    public function viewUser()
    {
       $userData = DB::table('bu_country as bc')
        ->join('bu', 'bc.bu_id', '=', 'bu.id')
        ->join('country', 'bc.country_id', '=', 'country.id')
        ->join('buh', 'bc.buh_id', '=', 'buh.id')
        ->select(
            'bc.id as id', 
            'bu.name as bu_name', 
            'country.name as country_name', 
            'buh.name as buh_name', 
            'buh.email as buh_email',
            'buh.nationality' // Include nationality here
        )
        ->paginate(10);

    // Pass the current page and per page values to the view
    $currentPage = $userData->currentPage();
    $perPage = $userData->perPage();

    // Pass the results to the view
    return view('Head_page', [
        'userData' => $userData,
        'currentPage' => $currentPage,
        'perPage' => $perPage,
        'countries' => DB::table('country')->get(),
        'businessUnits' => DB::table('bu')->get()
    ]);
    }
    // Save a new user
public function saveUser(Request $request)
{
    // Define the allowed email domains as a regular expression (e.g., gmail.com, yahoo.com)
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
                // Validates the email domain against the allowed domains
                if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                    $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                }
            }
        ],
        'nationality' => 'required|string|max:255', // Validation for nationality
        'bu_id' => 'required|integer', // Validation for Business Unit
        'country_id' => 'required|integer', // Validation for Country
    ]);

    // Insert into the users table
    $userId = DB::table('users')->insertGetId([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'role' => $request->input('role'),
        'password' => bcrypt('default'), // Password is hashed before saving
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Insert into the buh table
    $buhId = DB::table('buh')->insertGetId([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'nationality' => $validatedData['nationality'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Insert into the bu_country table
    DB::table('bu_country')->insert([
        'buh_id' => $buhId,
        'bu_id' => $validatedData['bu_id'],
        'country_id' => $validatedData['country_id'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // return redirect()->route('head.view-user')->with('success', 'User added successfully!');
    return redirect()->back()->with('success', 'User added successfully!');

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

    public function updateUser(Request $request, $id)
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
                // Update the user in the users table using the user ID
                DB::table('users')->where('id', $user->id)->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'), // Ensure email is updated
                    'role' => 'BUH',
                    'updated_at' => now(),
                ]);
            } else {
                Log::error('User not found for BUH email: ' . $buh->email);
            }
    
            // Update the BUH's information in the buh table
            DB::table('buh')->where('id', $buhId)->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'), // Ensure email is updated
                'nationality' => $nationality, // Use the resolved nationality value
                'updated_at' => now(),
            ]);
    
            // Update the bu_country table
            DB::table('bu_country')->where('id', $id)->update([
                'bu_id' => $request->input('bu_id'),
                'country_id' => $request->input('country_id'),
                'updated_at' => now(),
            ]);
    
            Log::info('User and BUH update completed successfully.');
    
            // Redirect or return a response indicating success
            // return redirect()->route('head.view-user')->with('success', 'User updated successfully!');
            return redirect()->back()->with('success', 'User updated successfully!');
    
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
        return redirect()->back()->with('success', 'User deleted successfully!');
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback the transaction on error
        Log::error('Error deleting user: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to delete user.');
    }
}


    // View contact details
    public function viewContact($contact_pid)
    {

    }

}
