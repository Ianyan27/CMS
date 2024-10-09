<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function viewUser()
    {
        // Get the currently authenticated user
        $currentUser = Auth::user();
        Log::info("User in view user: " . $currentUser);
        // Check the role of the current user
        if ($currentUser->role === 'Admin') {
            // If the user is an Admin, retrieve all users
            $userData = User::paginate(10);
        } elseif ($currentUser->role === 'BUH') {
            // If the user is a BUH, retrieve only users with the role 'Sales_Agent'
            $userData = User::where('role', 'Sales_Agent')->paginate(10);
        } elseif ($currentUser->role === 'Head') {
            // If the user is a Head, retrieve all users
            $userData = User::where('role', 'BUH')->paginate(10);
        } else {
            // Handle other roles or redirect if the role doesn't match expected ones
            return redirect()->route('home')->with('error', 'You do not have permission to view this page.');
        }

        // Pass the user data to the view
        return view('User_List_Page', [
            'userData' => $userData
        ]);
    }
}
