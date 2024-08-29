<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function microsoftLogin(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        $owners = Owner::where('owner_email_id', $request->email)->first();;

        Log::info('User Email :' . $user . '\n');
        Log::info('Owners Email:' . $owners . '\n');


        if ($user) {
            // Log the user in manually
            Auth::login($user);

            // Store user information including role in session
            $request->session()->put('name', $user->name);
            $request->session()->put('email', $user->email);
            $request->session()->put('role', $user->role); // Assuming 'role' is a column in the users table

            Log::info('Checking User table \n');

            Log::info('The role: ' . $user->role);
            // Redirect to the intended page
            return redirect()->intended('view-user');
        }

        // else if ($owners) {
        //     // Log the user in manually
        //     Auth::login($owners);

        //     // Store user information including role in session
        //     $request->session()->put('name', $owners->owner_name);
        //     $request->session()->put('email', $owners->owner_email_id);
        //     $request->session()->put(
        //         'role',
        //         'Sales_Agent'
        //     );

        //     $role = $request->session()->put('role', 'Sales_Agent'); // Assuming 'role' is a column in the users table

        //     Log::info('The role: ' . $role);


        //     // Redirect to the intended page
        //     return redirect()->intended('view-user');
        // }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('email'));
    }


    public function logout(Request $request)
    {

        $request->session()->forget(['name', 'email', 'role']);

        // Log the user out
        Auth::logout();

        // Invalidate the user's session and regenerate the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the login page or any other page you prefer
        return redirect()->route('login');
    }
}
