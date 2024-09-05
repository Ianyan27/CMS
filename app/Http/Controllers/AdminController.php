<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function index()
    {
        $userData = User::paginate(10);
        return view('User_List_Page', [
            'userData' => $userData
        ]);
    }

    public function viewUser()
    {
        $userData = User::paginate(10);
        return view('User_List_Page', ['userData' => $userData]);
    }

    public function contacts()
    {
        $contacts = Contact::paginate(10);
        $contactArchive = ContactArchive::paginate(10);
        $contactDiscard = ContactDiscard::paginate(10);
        return view('Contact_Listing', [
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function saveUser(Request $request)
    {

        // Define the allowed email domains
        $allowedDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        $domainRegex = implode('|', array_map(function ($domain) {
            return preg_quote($domain, '/');
        }, $allowedDomains));

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
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
                }
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        // hard code role
        $role = " ";
        // Create a new user
        User::create([
            'role' => null,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'password' => bcrypt($request->password), // Encrypt the password
        ]);
        return redirect()->route('admin#index')->with('success', 'User created successfully');
    }

    public function editUser($id){
        $editUser = User::find($id);
        return view('Edit_User_Detail_Page', ['editUser' => $editUser]);
    }

    public function updateUser(Request $request, $id)
    {
        $updateUser = User::find($id);
        $updateUser->update([
            $updateUser->name = $request->input('name'),
            $updateUser->email = $request->input('email'),
            $updateUser->role = $request->input('role'),
        ]);

        return redirect()->route('admin#index')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin#index')->with('success', 'User Deleted Successfully');
    }

    public function viewContact($contact_pid)
    {
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */
        $editContact = Contact::where('contact_pid', $contact_pid)->first();
        $user = Auth::user();
        $engagements = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        $engagementsArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_pid)->get();
        $updateEngagement = $engagements->first();
        return view('Edit_Contact_Detail_Page')->with([
            'user' => $user,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive
        ]);
    }
}
