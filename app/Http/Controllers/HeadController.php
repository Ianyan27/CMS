<?php

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
    // Display a listing of users
    public function index()
    {
        // Using Laravel's query builder syntax to retrieve the required data
        $userData = DB::table('bu_country as bc')
            ->join('bu as bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country as c', 'bc.country_id', '=', 'c.id')
            ->join('buh as buh', 'bc.buh_id', '=', 'buh.id')
            ->select('bc.id as id', 'bu.name as bu_name', 'c.name as country_name', 'buh.name as buh_name', 'buh.email as buh_email')
            ->paginate(10); // Adding pagination for the results

        // Retrieve all entries from the country table
        $countries = DB::table('country')->get();

        // Retrieve all entries from the bu table
        $businessUnits = DB::table('bu')->get();

        // Pass the results to the view
        return view('Head_page', [
            'userData' => $userData,
            'countries' => $countries,
            'businessUnits' => $businessUnits
        ]);
    }

    public function viewUser()
    {
        // Using Laravel's query builder syntax to retrieve the required user data
        $userData = DB::table('bu_country as bc')
            ->join('bu as bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country as c', 'bc.country_id', '=', 'c.id')
            ->join('buh as buh', 'bc.buh_id', '=', 'buh.id')
            ->select('bc.id as id', 'bu.name as bu_name', 'c.name as country_name', 'buh.name as buh_name', 'buh.email as buh_email')
            ->paginate(10); // Adding pagination for the results

        // Retrieve all entries from the country table
        $countries = DB::table('country')->get();

        // Retrieve all entries from the bu table
        $businessUnits = DB::table('bu')->get();

        // Pass the results to the view
        return view('Edit_Head_page', [
            'userData' => $userData,
            'countries' => $countries,
            'businessUnits' => $businessUnits
        ]);
    }

    // Save a new user
    public function saveUser(Request $request)
    {
        // Step 1: Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users,email', // Ensure email is unique in the user table
            'role' => 'required|string',
            'bu_id' => 'required|exists:bu,id', // Validate BU ID exists in the bu table
            'country_id' => 'required|exists:country,id', // Validate country ID exists in the country table
            'nationality' => 'required'
        ]);
    
        // Step 2: Save the BUH name, email, and role in the user table
     DB::table('users')->insertGetId([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => 'hannah uwu', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Step 3: Insert the BUH's name and email into the bu table and retrieve the inserted BU ID
        $buhId = DB::table('buh')->insertGetId([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'nationality' => $validatedData['nationality'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Step 4: Retrieve the country ID from the country table (already validated in Step 1)
        $countryId = $validatedData['country_id'];
        $buId = $validatedData['bu_id'];
    
        // Step 5: Insert the BUH ID, BU ID, and Country ID into the bu_country table
        DB::table('bu_country')->insert([
            'buh_id' => $buhId,
            'bu_id' => $buId,
            'country_id' => $countryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Redirect or return a response indicating success
        return redirect()->route('head.view-users')->with('success', 'User added successfully!');
    }
    
// Edit user details
public function editUser($id)
{
       // Using Laravel's query builder to retrieve user data by ID
    $user = DB::table('bu_country as bc')
        ->join('bu as bu', 'bc.bu_id', '=', 'bu.id')
        ->join('country as c', 'bc.country_id', '=', 'c.id')
        ->join('buh as buh', 'bc.buh_id', '=', 'buh.id')
        ->select('bc.id as id', 'bu.name as bu_name', 'c.name as country_name', 'buh.name as buh_name', 'buh.email as buh_email', 'buh.nationality')
        ->where('bc.id', $id)
        ->first();

    // Retrieve all entries from the country table
    $countries = DB::table('country')->get();

    // Retrieve all entries from the bu table
    $businessUnits = DB::table('bu')->get();

    // Pass the results to the edit view (you might have a separate edit view)
    return view('edit-user', [
        'users' => $user,
        'countries' => $countries,
        'businessUnits' => $businessUnits
    ]);
}

// Update user details
public function updateUser(Request $request, $id)
{
    // Step 1: Validate the incoming request data
    $validatedData = $request->validate([
        'name' => 'required',
        'email' => 'required', // Ensure email is unique except for the current user
        'role' => 'required',
        'bu_id' => 'required', // Validate BU ID exists in the bu table
        'country_id' => 'required', // Validate country ID exists in the country table
        'nationality' => 'required'
    ]);

    // Step 2: Update the user in the users table
    DB::table('users')->where('id', $id)->update([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'role' => 'BUH',
        'updated_at' => now(),
    ]);

    // Step 3: Update the BUH's information in the buh table
    DB::table('buh')->where('email', $validatedData['email'])->update([
        'name' => $validatedData['name'],
        'nationality' => 'test',
        'updated_at' => now(),
    ]);

    // Step 4: Update the bu_country table with the new BU and country IDs
    DB::table('bu_country')->where('buh_id', $id)->update([
        'bu_id' => $validatedData['bu_id'],
        'country_id' => $validatedData['country_id'],
        'updated_at' => now(),
    ]);

    // Redirect or return a response indicating success
    return redirect()->route('head.view-users')->with('success', 'User updated successfully!');
}


    // Delete a user
    public function deleteUser($id)
    {

    }

    // View contact details
    public function viewContact($contact_pid)
    {

    }

}
