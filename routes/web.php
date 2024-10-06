<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BUHController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\CSVDownloadController;
use App\Http\Controllers\DiscardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\SaleAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\HubspotContactController;
use App\Http\Controllers\SaleAgentsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Default Route
Route::get('/', function () {
    return view('Login');
})->name('login');

Route::post('/get-bu-data', [SaleAdminController::class, 'getBUData'])->name('get.bu.data');

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
        } else if (Auth::user()->role == 'Sales_Admin') {
            return redirect()->route('sales-admin#index');
        } else if (Auth::user()->role == 'Head') {
            return redirect()->route('head.index');
        }
    }
    return redirect()->route('login')->withErrors(['role' => 'Unauthorized access.']);
});


// Route::middleware(['auth'])->group(function () {
//     Route::get('/view-user', [AdminController::class, 'viewUser'])->name('view-user');
// });


Route::group(['prefix' => 'admin'], function () {
    // Admin Routes
    Route::get('/', [
        AdminController::class,
        'index'
    ])->name('admin#index');

    Route::get('/view-user', [
        AdminController::class,
        'viewUser'
    ])->name('admin#view-user');

    // User Routes
    Route::get('/edit-user/{id}', [
        AdminController::class,
        'editUser'
    ])->name('admin#edit-user');

    Route::post('/update-user/{id}', [
        AdminController::class,
        'updateUser'
    ])->name('admin#update-user');

    Route::delete('/delete-user/{id}', [
        AdminController::class,
        'deleteUser'
    ])->name('admin#delete-user');

    Route::post('/save-user', [
        AdminController::class,
        'saveUser'
    ])->name('admin#save-user');

    // Contact Routes
    Route::get('/contacts', [
        AdminController::class,
        'contacts'
    ])->name('admin#contact-listing');

    Route::get('/view-contacts/{contact_pid}', [
        AdminController::class,
        'viewContact'
    ])->name('admin#view-contact');

    // CSV Import Routes
    Route::get('/import-csv', [
        AdminController::class, 'importCSV'
    ])->name('admin#importcsv');

    Route::post('/import', [
        BUHController::class,
        'import'
    ])->name('admin#import');

    // Get CSV Format
    Route::get('/get-csv', [
        CSVDownloadController::class,
        'downloadCSV'
    ])->name('admin#get-csv');

    // Sales Admin Routes
    Route::get('/sales_admin', [
        SaleAdminController::class,
        'index'
    ])->name('admin#sales-admin');

    // Hubspot Contact Routes
    Route::get('/hubspot-contact', [
        ContactController::class,
        'hubspotContacts'
    ])->name('admin#hubspot-contact');

    Route::post('/submit-hubspot-contacts', [
        HubspotContactController::class,
        'submitHubspotContacts'
    ])->name('admin#submit-hubspot-contacts');

    // Sales Agent
    Route::get('/sale-agents', [
        SaleAgentsController::class,
        'saleAgent'
    ])->name('admin#viewSaleAgent');

    Route::get('/view-sale-agent/{id}', [
        SaleAgentsController::class,
        'viewSaleAgent'
    ])->name('admin#view-sale-agent');

    Route::post('/update-sale-agent/{id}', [
        SaleAgentsController::class,
        'updateSaleAgent'
    ])->name('admin#update-sale-agent');

    // Transfer Contact Routes
    Route::get('/transfer-contacts/{owner_pid}', [
        BUHController::class,
        'transferContact'
    ])->name('admin#transfer-contact');

    Route::post('/transfer', [
        BUHController::class,
        'transfer'
    ])->name('admin#transfer');

    // Delete Activity Route
    // Delete the activity from the archive activities table
    Route::post('/delete-activity/{engagement_pid}', [
        ContactController::class,
        'deleteActivity'
    ])->name('admin#deleteActivity');
    //Delete the activity of the archive contacts
    Route::post('/delete-activity/{engagement_archive_pid}', [
        ContactController::class,
        'deleteArchivedActivity'
    ])->name('admin#deleteArchivedActivity');

    Route::get('/delete-archive-activity/{engagement_archive_pid}', [
        ContactController::class,
        'deleteArchiveActivity'
    ])->name('admin#deleteArchiveActivity');
    // Move the Activity in the archive activities
    Route::post('/archive-activity/{engagement_pid}', [
        ContactController::class,
        'archiveActivities'
    ])->name('admin#archiveActivities');
    // Update Contact Route
    Route::post('/save-contact/{contact_pid}/{id}', [
        ContactController::class,
        'updateContact'
    ])->name('admin#update-contact');
    Route::get('/edit-contact/{contact_pid}', [
        ContactController::class,
        'editContact'
    ])->name('admin#edit-contact');
    Route::post('/save-contact/{contact_pid}/{id}', [
        AdminController::class,
        'updateContact'
    ])->name('admin#save-edit-contact');
    Route::post('/save-activity/{contact_pid}', [
        ContactController::class,
        'saveActivity'
    ])->name('admin#save-activity');
    Route::get('/view-archive/{contact_archive_pid}', [
        ArchiveController::class,
        'viewArchive'
    ])->name('admin#archive-view');
    Route::post('/retrieve-activity/{id}', [
        ArchiveController::class,
        'retrieveActivity'
    ])->name('admin#retrieveArchivedActivity');
    Route::post('/save-activity/{contact_pid}', [
        AdminController::class,
        'saveActivity'
    ])->name('admin#save-activity');
});

Route::group(['prefix' => 'sales-agent'], function () {
    Route::get('/', [
        ContactController::class,
        'index'
    ])->name('sales-agent#index');
    Route::get('/contact-listing', [
        ContactController::class,
        'contactsByOwner'
    ])->name('contact-listing');
    Route::get('view-contact/{contact_pid}', [
        ContactController::class,
        'viewContact'
    ])->name('contact#view');
    Route::get('/edit-contact/{contact_pid}', [
        ContactController::class,
        'editContact'
    ])->name('contact#edit');
    Route::post('/save-contact/{contact_pid}/{owner_pid}', [
        ContactController::class,
        'updateContact'
    ])->name('contact#update-contact');
    Route::get('/edit-archive/{contact_archive_pid}', [
        ArchiveController::class,
        'editArchive'
    ])->name('archive#edit');
    Route::get('/view-archive/{contact_archive_pid}', [
        ArchiveController::class,
        'viewArchive'
    ])->name('archive#view');
    Route::post('/save-archive/{contact_archive_pid}/{owner_pid}', [
        ArchiveController::class,
        'updateArchive'
    ])->name('archive#update-archive');
    Route::get('/edit-discard/{contact_discard_pid}', [
        DiscardController::class,
        'editDiscard'
    ])->name('discard#edit');
    Route::get('/view-discard/{contact_discard_pid}', [
        DiscardController::class,
        'viewDiscard'
    ])->name('discard#view');
    Route::post('/save-discard/{contact_discard_pid}', [
        DiscardController::class,
        'updateDiscard'
    ])->name('discard#update-discard');
    Route::post('/save-activity/{contact_pid}', [
        ContactController::class,
        'saveActivity'
    ])->name('contact#save-activity');
    //Archive Activity
    Route::post('/archive-activities/{engagement_archive_pid}', [
        ContactController::class,
        'archiveActivity'
    ])->name('archiveActivity');
    //Archive Contacts
    Route::post('/archive-activities/{engagement_archive_pid}', [
        ContactController::class,
        'archiveContactActivities'
    ])->name('archiveContactActivities');
    Route::post('/delete-archive-activity/{engagement_archive_pid}', [
        ContactController::class,
        'deleteArchiveActivity'
    ])->name('deleteArchiveActivity');

    Route::post('/delete-activity/{engagement_pid}', [
        ContactController::class,
        'deleteActivity'
    ])->name('deleteActivity');
    Route::post('/retrieve-activity/{id}', [
        ContactController::class,
        'retrieveActivity'
    ])->name('retrieveActivity');
    Route::post('/save-archive-activity/{contact_archive_pid}', [
        ArchiveController::class,
        'saveActivity'
    ])->name('archive#save-activity');
    Route::get('/edit-activity/{contact_id}/{activity_id}', [
        ContactController::class,
        'editActivity'
    ])->name('contact#update-activity');
    Route::post('/contact/{contact_pid}/activity/{activity_id}/update', [
        ContactController::class,
        'saveUpdateActivity'
    ])
        ->name('contact#save-update-activity');
    Route::post('/archive/{contact_archive_pid}/activity/{activity_id}/update', [
        ArchiveController::class,
        'updateActivity'
    ])->name('archive#update-activity');
    Route::post('/save-discard-activity/{contact_discard_pid}', [
        DiscardController::class,
        'saveDiscardActivity'
    ])->name('discard#save-discard-activity');
    Route::get('/edit-sale-agent/{owner_pid}', [
        OwnerController::class,
        'editOwner'
    ])->name('admin#update');
    Route::post('/update-owner/{owner_pid}', [
        OwnerController::class,
        'updateSaleAgent'
    ])->name('admin#update-owner');
});

Route::group(['prefix' => 'buh'], function () {
    Route::get('/', [
        BUHController::class,
        'index'
    ])->name('buh#index');
    Route::get('/view-user', [
        UserController::class,
        'viewUser'
    ])->name('view-user');
    Route::get('/import-csv', function () {
        return view('csv_import_form');
    })->name('importcsv');
    Route::post('/import', [
        BUHController::class,
        'import'
    ])->name('import');
    //get csv format
    Route::get('/get-csv', [
        CSVDownloadController::class,
        'downloadCSV'
    ])->name('get-csv');
    //HubspotS
    Route::get('/hubspot-contact', [
        ContactController::class,
        'hubspotContacts'
    ])->name('hubspot-contact');
    Route::post('/submit-hubspot-contacts', [
        HubspotContactController::class,
        'submitHubspotContacts'
    ])->name('submit-hubspot-contacts');
    Route::get('/owner', [
        OwnerController::class,
        'owner'
    ])->name('owner#view');
    Route::get('/view-owner/{owner_pid}', [
        OwnerController::class,
        'viewSaleAgent'
    ])->name('owner#view-owner');
    Route::post('/save-user', [
        BUHController::class,
        'saveUser'
    ])->name('owner#save-user');
    Route::get('/edit-owner/{owner_pid}', [
        OwnerController::class,
        'editOwner'
    ])->name('owner#update');
    Route::post('/update-sale-agent/{id}', [
        OwnerController::class,
        'updateSaleAgent'
    ])->name('buh#update-sale-agent');
    Route::delete('/delete-sale-agent/{id}', [
        BUHController::class,
        'deleteOwner'
    ])->name('buh#delete-sale-agent');
    Route::get('/view-contact/{contact_pid}', [
        OwnerController::class,
        'viewContact'
    ])->name('owner#view-contact');
    Route::get('/transfer-contacts/{owner_pid}', [
        BUHController::class,
        'transferContact'
    ])->name('owner#transfer-contact');
    Route::post('/transfer', [
        BUHController::class,
        'transfer'
    ])->name('owner#transfer');
    // Route::post('/assign-contact', [
    //     BUHController::class, 'assignContacts'
    // ])->name('owner#assign-contact');
    Route::post('/update-status-owner/{owner_pid}', [
        BUHController::class,
        'updateStatusOwner'
    ])->name('owner#update-status-owner');
    Route::get('/progress', [
        BUHController::class,
        'getProgress'
    ])->name('progress');
});

// Define routes for the Head role
Route::group(['prefix' => 'head', 'as' => 'head.'], function () {
    Route::get('/', [HeadController::class, 'index'])->name('index');

    // View user details
    Route::get('/view-user', [HeadController::class, 'viewUser'])->name('view-user');

    // Save a new user
    Route::post('/save-user', [HeadController::class, 'saveUser'])->name('save-user');

    // Edit user details
    Route::get('/edit-user/{id}', [HeadController::class, 'editUser'])->name('edit-user');

    // Update user details (change this line)
    Route::put('/update-user/{id}', [HeadController::class, 'updateUser'])->name('update-user'); // Change from POST to PUT
    
    // Delete a user
    Route::delete('/delete-user/{id}', [HeadController::class, 'deleteUser'])->name('delete-user');

    // View contact details
    Route::get('/view-contact/{contact_pid}', [HeadController::class, 'viewContact'])->name('view-contact');
});

Route::group(['prefix' => 'sales-admin'], function () {
    Route::get('/', [
        SaleAdminController::class,
        'index'
    ])->name('sales-admin#index');
});
