<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller{

    public function index(){
        $userData = User::paginate(10);
        return view ('User_List_Page', [
            'userData'=>$userData
        ]);
    }

    public function viewUser(){
        $userData = User::paginate(10);
        return view ('User_List_Page', ['userData'=>$userData]);
    }

    public function contacts(){
        $contacts = Contact::paginate(10);
        $contactArchive = ContactArchive::paginate(10);
        $contactDiscard = ContactDiscard::paginate(10);
        return view('Contact_Listing', [
            'contacts'=>$contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function saveUser(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        User::create([
            'role' => null,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encrypt the password
        ]);
        return redirect()->route('view-user')->with('success', 'User created successfully');
    }

    public function editUser($id){
        $editUser = User::find($id);
        return view('Edit_User_Detail_Page', ['editUser'=>$editUser]);
    }

    public function updateUser(Request $request, $id){
        $updateUser = User::find($id);
        $updateUser->update([
        $updateUser->name = $request->input('name'),
        $updateUser->email = $request->input('email'),
        $updateUser->role = $request->input('role'),
        ]);

    return redirect()->route('view-user')->with('success', 'User updated successfully');
    }

    public function deleteUser($id){
        User::where('id', $id)->delete();
        return redirect()->route('view-user')->with('success', 'User Deleted Successfully');
    }
}