<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// Default Route
Route::get('/', function () {
    return view('User_List_Page');
})->name('user-list');

// Dashboard Route
Route::get('/dashboard', function () {
    return view('Dashboard');
})->name('dashboard');

// Sales Agent Route
Route::get('/salesagent', function () {
    return view('Sale_Agent_Page');
})->name('salesagent');

// Contact Listing Route
Route::get('/contact-listing', [ContactController::class, 'contacts'])->name('contact-listing');

// View Contact Route
Route::get('view_contact/{contact_pid}', [ContactController::class, 'viewContact'])->name('contact#view');

// Edit Contact Route
Route::get('/edit_contact/{contact_pid}', [ContactController::class, 'edit_contact'])->name('contact#edit');

// Update Contact Route
Route::post('/save_contact/{contact_pid}', [ContactController::class, 'updateContact'])->name('contact#update_contact');

// Save Activity Route
Route::post('/save_activity/{contact_pid}', [ContactController::class, 'saveActivity'])->name('contact#save_activity');

// Edit Activity Route
Route::get('/edit_activity/{fk_engagements__contact_pid}', [ContactController::class, 'updateActivity'])->name('contact#update_activity');

// Update Activity Route 
Route::post('/save_activity_update/{contact_pid}', [ContactController::class, 'saveUpdateActivity'])->name('contact#save_update_activity');

// Import Copy Route
Route::get('/importcopy', function () {
    return view('Import_File');
})->name('importcopy');

// Edit Contact Detail Route
Route::get('/editcontactdetail', function () {
    return view('Edit_Contact_Detail_Page');
})->name('editcontactdetail');
