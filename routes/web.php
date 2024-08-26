<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\ContactsImportController;
use App\Http\Controllers\CSVDownloadController;
use App\Http\Controllers\DiscardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Default Route
Route::get('/', function() {
    return view('Login');
})->name('login');
// Microsoft Login Route
Route::post('/microsoft-login', [AuthController::class, 'microsoftLogin'])->name('microsoft.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth:sanctum', 'verified'])->get('/view-user', function() {
    if (Auth::check()) {
        if (Auth::user()->role == 'Sales_Agent') {
            return redirect()->route('contact-listing');
        } elseif (Auth::user()->role == 'Admin') {
            return redirect()->route('view-user');
        } else if (Auth::user()->role == 'BUH'){
            return redirect()->route('view-user');
        } else if (Auth::user()->role == 'User'){
            return redirect()->route('view-user');
        }
    }
    return redirect()->route('login')->withErrors(['role' => 'Unauthorized access.']);
});

Route::group(['prefix' => 'Admin'], function(){
    Route::get('/', [UserController::class, 'viewUser'])->name('view-user');
    Route::get('/view-user', [UserController::class, 'viewUser'])->name('view-user');
    Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('user#edit-user');
    Route::post('/update-user/{id}', [UserController::class, 'updateUser'])->name('user#update-user');
    Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('user#delete-user');
    Route::post('/save-user', [UserController::class, 'saveUser'])->name('save-user');
});

Route::group(['prefix' => 'Sales_Agent'], function(){
    Route::get('/', [OwnerController::class, 'owner'])->name('owner#view');
    // Sales Agent Route
    Route::get('/sales-agent', [OwnerController::class, 'owner'])->name('owner#view');
    Route::get('/view-owner/{owner_pid}', [OwnerController::class, 'viewOwner'])->name('owner#view_owner');
    Route::get('/edit-owner/{owner_pid}', [OwnerController::class, 'editOwner'])->name('owner#update');
    Route::post('/update-owner/{owner_pid}', [OwnerController::class, 'updateOwner'])->name('owner#update_owner');

    // Contact Route
    Route::get('/contact-listing', [ContactController::class, 'contacts'])->name('contact-listing');

    // View Contact Route
    Route::get('view_contact/{contact_pid}', [ContactController::class, 'viewContact'])->name('contact#view');

    // Edit Contact Route
    Route::get('/edit_contact/{contact_pid}', [ContactController::class, 'editContact'])->name('contact#edit');

    // Update Contact Route
    Route::post('/save_contact/{contact_pid}', [ContactController::class, 'updateContact'])->name('contact#update_contact');

    // Archive Route
    Route::get('/edit_archive/{contact_archive_pid}', [ArchiveController::class, 'editArchive'])->name('archive#edit');

    // View Archive Route
    Route::get('/view_archive/{contact_archive_pid}', [ArchiveController::class, 'viewArchive'])->name('archive#view');
    Route::post('save_archive/{contact_archive_pid}', [ArchiveController::class, 'updateArchive'])->name('archive#update_archive');

    //Discard Route
    Route::get('/edit_discard/{contact_discard_pid}', [DiscardController::class, 'editDiscard'])->name('discard#edit');

    //View Discard Route
    Route::get('/view_discard/{contact_discard_pid}', [DiscardController::class, 'viewDiscard'])->name('discard#view');
    Route::post('/save_discard/{contact_discard_pid}', [DiscardController::class, 'updateDiscard'])->name('discard#update_discard');

    // Save Activity Route
    Route::post('/save_activity/{contact_pid}', [ContactController::class, 'saveActivity'])->name('contact#save_activity');

    // Edit Activity Route
    Route::get('/edit_activity/{contact_id}/{activity_id}', [ContactController::class, 'editActivity'])->name('contact#update_activity');

    // Update Activity Route
    Route::post('/contact/{contact_pid}/activity/{activity_id}/update', [ContactController::class, 'saveUpdateActivity'])
        ->name('contact#save_update_activity');

    // Save Discard Activity Route
    Route::post('/save_discard_activity/{contact_discard_pid}', [
        DiscardController::class, 'saveDiscardActivity'
        ])->name('discard#save_discard_activity');
});

Route::group(['prefix' => 'BUH'], function (){
    Route::get('/', [ContactsImportController::class, 'import'])->name('import');
    // Import Copy Route
    Route::get('/importcsv', function () {
        return view('csv_import_form');
    })->name('importcsv');
    Route::post('/import', [ContactsImportController::class, 'import'])->name('import');

    // Edit Contact Detail Route
    Route::get('/editcontactdetail', function () {
        return view('Edit_Contact_Detail_Page');
        
    })->name('editcontactdetail');
    //get csv format
    Route::get('/getCsv', [CSVDownloadController::class, 'downloadCSV'])->name('getCsv'); 
});