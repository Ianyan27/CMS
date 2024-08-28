<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{

    public function viewUser(){
        // Get the currently authenticated user
        $currentUser = Auth::user();
        // Check the role of the current user
        if ($currentUser->role === 'Admin') {
            // If the user is an Admin, retrieve all users
            $userData = User::paginate(10);
        } elseif ($currentUser->role === 'BUH') {
            // If the user is a BUH, retrieve only users with the role 'Sales_Agent'
            $userData = User::where('role', 'Sales_Agent')->paginate(10);
        } else {
            // Handle other roles or redirect if the role doesn't match expected ones
            return redirect()->route('home')->with('error', 'You do not have permission to view this page.');
        }
    
        // Pass the user data to the view
        return view('User_List_Page', [
            'userData' => $userData
        ]);
    }

    public function viewUserBUH(Request $request){
        // // Get the current logged-in user's fk_buh
        // $fkBuh = 10;

        // // Fetch users that match the fk_buh
        // $userData = User::where('fk_buh', 10)->paginate(10);

        // // Pass the filtered user data to the view
        // return view('User_List_Page', [
        //     'userData' => $userData
        // ]);

        $userData = User::paginate(10);
        return view('User_List_Page', [
            'userData' => $userData
        ]);
    }

    public function saveUser(Request $request){

        $request->validate([
            'role' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encrypt the password
        ]);
        return redirect()->route('view-user')->with('success', 'User created successfully');
    }

    public function editUser($id){
        $editUser = User::find($id);
        return view('Edit_User_Detail_Page', ['editUser' => $editUser]);
    }

    public function updateUser(Request $request, $id){
        $updateUser = User::find($id);
        $updateUser->name = $request->input('name');
        $updateUser->email = $request->input('email');
        $updateUser->role = $request->input('role');
        $updateUser->save();

        return redirect()->route('view-user')->with('success', 'User updated successfully');
    }

    public function deleteUser($id){
        User::where('id', $id)->delete();
        return redirect()->route('view-user')->with('success', 'User Deleted Successfully');
    }
}
