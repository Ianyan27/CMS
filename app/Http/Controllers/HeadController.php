<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use App\Models\Contact; // Import the Contact model
use App\Models\Engagement; // Import the Engagement model
use App\Models\EngagementArchive; // Import the EngagementArchive model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HeadController extends Controller
{
    // Display a listing of users
    public function index()
    {
        $userData = User::paginate(10);
        return view('Head_page', [
            'userData' => $userData,
        ]);
    }

    // View user details
    public function viewUser()
    {
        $userData = User::paginate(10);
        return view('Head_page', ['userData' => $userData]);
    }

    // Save a new user
    public function saveUser(Request $request)
{
    // Define the allowed email domains

    $allowedDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
    $domainRegex = implode('|', array_map(function ($domain) {
        return preg_quote($domain, '/');
    }, $allowedDomains));

    // Validate the request
    $request->validate([
        'name' => 'required|string|min:3|max:50',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users',
            function ($attribute, $value, $fail) use ($domainRegex) {
                if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                    $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                }
            },
        ],
        'password' => 'string',
    ]);
    $businessUnit = " ";
    // Create a new user
    try {
        User::create([
            'role' => 'BUH', // Consider assigning a proper role if needed
            'name' => $request->name,
            'email' => $request->email,
            'business_unit' => $request->businessUnit,
            'password' => 'uwu', // Encrypt the password
            
        ]);

        // Log success message
        Log::info('User created successfully: ' . $request->email);
        
        // Redirect with success message
        return redirect()->route('head.view-user')->with('success', 'User created successfully');

    } catch (\Exception $e) {
        // Log error message
        Log::error('Error creating user: ' . $e->getMessage());

        // Redirect with error message
       return redirect()->back()->with('error', 'Failed to create user');
    }
}

    // Edit user details
    public function editUser($id)
    {
        $editUser = User::find($id);
        return view('Edit_Head_Page', ['editUser' => $editUser]);
    }

    public function updateUser(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) use ($domainRegex) {
                    if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                        $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                    }
                },
            ],
            'password' => 'string',
        ]);
        // Find the user by ID
        $user = User::find($id);
    
        if ($user) {
            // Update user details
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->businessUnit = $request->input('business_unit');
            $user->save(); // Ensure save() is called to persist changes
    
            // Redirect with success message
            return redirect()->route('head.index')->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    // Delete a user
    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('head.index')->with('success', 'User deleted successfully');
    }

    // View contact details
    public function viewContact($contact_pid)
    {
        // Retrieve the contact record with the specified 'contact_pid' and check the role
        $editContact = Contact::where('contact_pid', $contact_pid)
                              ->whereHas('user', function ($query) {
                                  $query->where('role', 'BUH');
                              })
                              ->first();
    
        $user = Auth::user();
        $engagements = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        $engagementsArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_pid)->get();
        $updateEngagement = $engagements->first();
    
        return view('Edit_Contact_Detail_Page')->with([
            'user' => $user,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive,
        ]);
    }
    
}
