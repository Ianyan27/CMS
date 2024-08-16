<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', function () {
    return view('Login');
});

/* ADMIN ROUTE */

Route::get('/', function () {
    return view('User_List_Page');
});

Route::get('/dashboard', function () {
    return view('Dashboard');
});

// Route::get('/contactdetails', function () {
//     return view('Contact_Details');
// });

Route::get('/salesagent', function () {
    return view('Sale_Agent_Page');
});

Route::get("/dashboard", function () {
    return view('Dashboard');
});

Route::get("/contact-listing", [ContactController::class, 'index']);
// Route::get("/contact-listing", [ContactController::class, 'second_index']);


Route::get('/importcopy', function () {
    return view('Import_File');
});

Route::get('/editcontactdetail', function () {
    return view('Edit_Contact_Detail_Page');
});

Route::get('/delete', function () {
    return view('DeletePrompt');
});
