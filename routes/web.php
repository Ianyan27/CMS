<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

/* ADMIN ROUTE */

Route::get('/', function () {
    return view('User_List_Page');
});
Route::get('/dashboard', function(){
    return view ('Dashboard');
});

Route::get('/contactdetails', function () {
    return view('Contact_Details');
});

Route::get('/salesagent', function () {
    return view('Sale_Agent_Page');
});

ROute::get("/dashboard", function () {
    return view('Dashboard');
});

Route::get('/import-copy-2nd', function(){
    return view('Import_File');
});