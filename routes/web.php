<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BUHController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\CSVDownloadController;
use App\Http\Controllers\DiscardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HubspotContactController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Default Route
Route::get('/', function () {
    return view('Login');
})->name('login');

// Microsoft OAuth Login
Route::get('login/microsoft', [AuthController::class, 'redirectToMicrosoft'])->name('login.microsoft');
Route::get('auth/callback', [AuthController::class, 'handleMicrosoftCallback'])->name('callback');

// Microsoft Login Route
Route::post('/microsoft-login', [AuthController::class, 'microsoftLogin'])->name('microsoft.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth:sanctum', 'verified'])->get('/view-user', function () {

    if (Auth::check()) {
        if (Auth::user()->role == 'Sales_Agent') {
            return redirect()->route('sales-agent#index');
        } elseif (Auth::user()->role == 'Admin') {
            return redirect()->route('admin#index');
        } else if (Auth::user()->role == 'BUH') {
            return redirect()->route('buh#index');
        }
    }
    return redirect()->route('login')->withErrors(['role' => 'Unauthorized access.']);
});

// Route::middleware(['auth'])->group(function () {
//     Route::get('/view-user', [AdminController::class, 'viewUser'])->name('view-user');
// });


Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [
        AdminController::class, 'index'
        ])->name('admin#index');
    Route::get('/view-user', [
        AdminController::class, 'viewUser'
        ])->name('admin#view-user');
    Route::get('/edit-user/{id}', [
        AdminController::class, 'editUser'
        ])->name('user#edit-user');
    Route::post('/update-user/{id}', [
        AdminController::class, 'updateUser'
        ])->name('user#update-user');
    Route::delete('/delete-user/{id}', [
        AdminController::class, 'deleteUser'
        ])->name('user#delete-user');
    Route::post('/save-user', [
        AdminController::class, 'saveUser'
        ])->name('user#save-user');
    Route::get('/contacts', [
        AdminController::class, 'contacts'
        ])->name('admin#contact-listing');
    Route::get('/view-contacts/{contact_pid}', [
        AdminController::class, 'viewContact'
        ])->name('admin#view-contact');
});
Route::group(['prefix' => 'sales-agent'], function () {
    Route::get('/', [
        ContactController::class, 'index'
        ])->name('sales-agent#index');
    Route::get('/contact-listing', [
        ContactController::class, 'contactsByOwner'
        ])->name('contact-listing');
    Route::get('view-contact/{contact_pid}', [
        ContactController::class, 'viewContact'
        ])->name('contact#view');
    Route::get('/edit-contact/{contact_pid}', [
        ContactController::class, 'editContact'
        ])->name('contact#edit');
    Route::post('/save-contact/{contact_pid}/{owner_pid}', [
        ContactController::class, 'updateContact'
        ])->name('contact#update-contact');
    Route::get('/edit-archive/{contact_archive_pid}', [
        ArchiveController::class, 'editArchive'
        ])->name('archive#edit');
    Route::get('/view-archive/{contact_archive_pid}', [
        ArchiveController::class, 'viewArchive'
        ])->name('archive#view');
    Route::post('/save-archive/{contact_archive_pid}/{owner_pid}', [
        ArchiveController::class, 'updateArchive'
        ])->name('archive#update-archive');
    Route::get('/edit-discard/{contact_discard_pid}', [
        DiscardController::class, 'editDiscard'
        ])->name('discard#edit');
    Route::get('/view-discard/{contact_discard_pid}', [
        DiscardController::class, 'viewDiscard'
        ])->name('discard#view');
    Route::post('/save-discard/{contact_discard_pid}', [
        DiscardController::class, 'updateDiscard'
        ])->name('discard#update-discard');
    Route::post('/save-activity/{contact_pid}', [
        ContactController::class, 'saveActivity'
        ])->name('contact#save-activity');
    Route::post('/save-archive-activity/{contact_archive_pid}', [
        ArchiveController::class, 'saveActivity'
        ])->name('archive#save-activity');
    Route::get('/edit-activity/{contact_id}/{activity_id}', [
        ContactController::class, 'editActivity'
        ])->name('contact#update-activity');
    Route::post('/contact/{contact_pid}/activity/{activity_id}/update', [
        ContactController::class, 'saveUpdateActivity'])
        ->name('contact#save-update-activity');
    Route::post('/archive/{contact_archive_pid}/activity/{activity_id}/update', [
        ArchiveController::class, 'updateActivity'])->name('archive#update-activity');
    Route::post('/save-discard-activity/{contact_discard_pid}', [
        DiscardController::class,
        'saveDiscardActivity'
    ])->name('discard#save-discard-activity');
});

Route::group(['prefix' => 'buh'], function () {
    Route::get('/', [
        BUHController::class, 'index'
        ])->name('buh#index');
    Route::get('/view-user', [
        UserController::class, 'viewUser'
    ])->name('view-user');
    Route::get('/import-csv', function () {
        return view('csv_import_form');
    })->name('importcsv');
    Route::post('/import', [
        BUHController::class, 'import'
    ])->name('import');
    //get csv format
    Route::get('/get-csv', [
        CSVDownloadController::class, 'downloadCSV'
    ])->name('get-csv');
    //HubspotS
    Route::get('/hubspot-contact', [
        ContactController::class, 'hubspotContacts'
        ])->name('hubspot-contact');
    Route::post('/submit-hubspot-contacts', [
        HubspotContactController::class, 'submitHubspotContacts'
        ])->name('submit-hubspot-contacts');
    Route::get('/owner', [
        OwnerController::class, 'owner'
        ])->name('owner#view');
    Route::get('/view-owner/{owner_pid}', [
        OwnerController::class, 'viewOwner'
        ])->name('owner#view-owner');
    Route::post('/save-user', [
        BUHController::class, 'saveUser'
        ])->name('owner#save-user');
    Route::get('/edit-owner/{owner_pid}', [
        OwnerController::class, 'editOwner'
        ])->name('owner#update');
    Route::post('/update-owner/{owner_pid}', [
        OwnerController::class, 'updateOwner'
        ])->name('owner#update-owner');
    Route::delete('/delete-owner/{owner_pid}', [
        BUHController::class, 'deleteOwner'
        ])->name('owner#delete');
    Route::get('/view-contact/{contact_pid}', [
        OwnerController::class, 'viewContact'
        ])->name('owner#view-contact');
});
