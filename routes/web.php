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

Route::post('/get-bu-data', [
    SaleAdminController::class,
    'getBUData'
])->name('get.bu.data');

Route::post('/get-buh-by-country', [
    SaleAdminController::class,
    'getBUHByCountry'
])->name('get.buh.by.country');


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
            return redirect()->route('head#index');
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

    Route::post('/delete-user/{id}', [
        AdminController::class,
        'deleteUser'
    ])->name('admin#delete-user');

    Route::post('/save-user', [
        AdminController::class,
        'saveUser'
    ])->name('admin#save-new-user');

    // Contact Routes
    Route::get('/contacts', [
        AdminController::class,
        'contacts'
    ])->name('admin#contact-listing');

    Route::get('/view-contacts/{contact_pid}', [
        AdminController::class,
        'viewContact'
    ])->name('admin#view-contact');

    Route::get('/view-transferable-contact/{contact_pid}/{type}', [
        AdminController::class,
        'viewTransferableContact'
    ])->name('admin#view-transferable-contact');

    // CSV Import Routes
    Route::get('/import-csv', [
        AdminController::class,
        'importCSV'
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
    Route::get('/transfer-contacts/{id}', [
        AdminController::class,
        'transferContact'
    ])->name('admin#transfer-contact');

    Route::post('/transfer', [
        AdminController::class,
        'transfer'
    ])->name('admin#transfer');

    // Delete Activity Route
    Route::post('/admin-archive-activities/{engagement_archive_pid}', [
        AdminController::class,
        'archiveContactActivities'
    ])->name('admin#archiveActivity');
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

    Route::get('/admin/delete-archive-activity/{engagement_archive_pid}', [
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

    //Retrive Archived Activities
    Route::post('/retrieve-activity/{id}', [
        ArchiveController::class,
        'retrieveActivity'
    ])->name('admin#retrieveArchivedActivity');
    Route::post('/save-activity/{contact_pid}', [
        AdminController::class,
        'saveActivity'
    ])->name('admin#save-activity');
    Route::get('/view-buh', [
        AdminController::class,
        'viewBUH'
    ])->name('admin#view-buh');

    Route::get('/view-buh/{id}', [
        AdminController::class,
        'viewBUHDetails'
    ])->name('admin#view-buh-detail');

    Route::post('/save-buh', [
        AdminController::class,
        'saveBUH'
    ])->name('admin#save-buh');

    Route::delete('/delete-buh/{id}', [
        AdminController::class,
        'deleteBUH'
    ])->name('admin#delete-buh');

    Route::post('/update-status-sale-agent/{id}', [
        AdminController::class,
        'updateStatusSaleAgent'
    ])->name('admin#update-status-sale-agent');
    Route::get('/progress', [
        AdminController::class,
        'getProgress'
    ])->name('progress');


    // BU and Country routes
    Route::get('/bu-country', [
        SaleAdminController::class,
        'buCountry'
    ])->name('admin#bu-country');
    Route::post('/add-bu', [
        SaleAdminController::class,
        'saveBU'
    ])->name('admin#add-bu');
    Route::post('/add-country', [
        SaleAdminController::class,
        'saveCountry'
    ])->name('admin#add-country');
    Route::post('/update-country/{id}', [
        SaleAdminController::class,
        'updateCountry'
    ])->name('admin#update-country');
    Route::post('/update-bu/{id}', [
        SaleAdminController::class,
        'updateBU'
    ])->name('admin#update-bu');
    Route::post('/delete-country/{id}', [
        SaleAdminController::class,
        'deleteCountry'
    ])->name('admin#delete-country');
    Route::post('/delete-bu/{id}', [
        SaleAdminController::class,
        'deleteBU'
    ])->name('admin#delete-bu');
});

Route::group(['prefix' => 'sales-agent'], function () {
    Route::get('/', [
        ContactController::class,
        'index'
    ])->name('sales-agent#index');
    Route::get('/contact-listing', [
        ContactController::class,
        'contactsByOwner'
    ])->name('sale-agent#contact-listing');
    Route::get('view-contact/{contact_pid}', [
        ContactController::class,
        'viewContact'
    ])->name('sale-agent#view');
    Route::get('/edit-contact/{contact_pid}', [
        ContactController::class,
        'editContact'
    ])->name('contact#edit');
    Route::post('/save-contact/{contact_pid}/{id}', [
        ContactController::class,
        'updateContact'
    ])->name('sale-agent#update-contact');
    Route::get('/edit-archive/{contact_archive_pid}', [
        ArchiveController::class,
        'editArchive'
    ])->name('archive#edit');
    Route::get('/view-archive/{contact_archive_pid}', [
        ArchiveController::class,
        'viewArchive'
    ])->name('archive#view');
    Route::post('/save-archive/{contact_archive_pid}/{id}', [
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
    Route::post('/archive-activities/{engagement_pid}', [
        ContactController::class,
        'archiveActivity'
    ])->name('archiveActivity');
    //Archive Contacts
    Route::post('/sale-agent-archive-activities/{engagement_archive_pid}', [
        ContactController::class,
        'archiveContactActivities'
    ])->name('sale-agent#archiveContactActivities');

    Route::post('/sale-agent/delete-archive-activity/{engagement_archive_pid}', [
        ContactController::class,
        'deleteArchiveActivity'
    ])->name('sale-agent#deleteArchiveActivity');

    Route::post('/delete-activity/{engagement_pid}', [
        ContactController::class,
        'deleteActivity'
    ])->name('sale-agent#deleteActivity');
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
    ])->name('contact#save-update-activity');
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
    Route::post('/update-owner/{id}', [
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
    Route::get('/import-csv', [
        BUHController::class,
        'index'
    ])->name('buh#import-csv');
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
    Route::get('/sale-agent', [
        OwnerController::class,
        'saleAgent'
    ])->name('buh#view');
    Route::get('/view-sale-agent/{id}', [
        OwnerController::class,
        'viewSaleAgent'
    ])->name('buh#view-sale-agent');
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
    ])->name('buh#view-contact');
    Route::get('/transfer-contacts/{owner_pid}', [
        BUHController::class,
        'transferContact'
    ])->name('buh#transfer-contact');
    Route::post('/transfer', [
        BUHController::class,
        'transfer'
    ])->name('buh#transfer');
    // Route::post('/assign-contact', [
    //     BUHController::class, 'assignContacts'
    // ])->name('owner#assign-contact');
    Route::post('/update-status-sale-agent/{id}', [
        BUHController::class,
        'updateStatusOwner'
    ])->name('buh#update-status-sale-agent');
    Route::get('/progress', [
        BUHController::class,
        'getProgress'
    ])->name('progress');
    Route::get('/contact-listing', [
        BUHController::class,
        'contactsByBUH'
    ])->name('buh#contact-listing');
});

Route::group(['prefix' => 'head', 'as' => 'head#'], function () {
    Route::get('/', [
        HeadController::class,
        'index'
    ])->name('index');

    // View user details
    Route::get('/view-user', [
        HeadController::class,
        'viewUser'
    ])->name('head#view-user');

    // Save a new user
    Route::post('/save-user', [
        HeadController::class,
        'saveUser'
    ])->name('save-user');

    // Edit user details
    Route::get('/edit-user/{id}', [HeadController::class, 'editUser'])->name('edit-user');

    // Update user details (change this line)
    Route::put('/update-buh/{id}', [
        HeadController::class,
        'updateBUH'
    ])->name('update-buh'); // Change from POST to PUT

    // Delete a user
    Route::delete('/delete-user/{id}', [
        HeadController::class,
        'deleteUser'
    ])->name('delete-user');

    // View contact details
    Route::get('/view-contact/{contact_pid}', [
        HeadController::class,
        'viewContact'
    ])->name('view-contact');
    Route::get('/view-buh-detail/{id}', [
        HeadController::class,
        'viewBUHDetails'
    ])->name('view-buh-detail');
});

Route::group(['prefix' => 'sales-admin'], function () {
    Route::get('/', [
        SaleAdminController::class,
        'index'
    ])->name('sales-admin#index');

    // BU and Country routes
    Route::get('/bu&country', [
        SaleAdminController::class,
        'buCountry'
    ])->name('sales-admin#bu-country');

    Route::post('/add-bu', [
        SaleAdminController::class,
        'saveBU'
    ])->name('sales-admin#add-bu');
    Route::post('/add-country', [
        SaleAdminController::class,
        'saveCountry'
    ])->name('sales-admin#add-country');
    Route::post('/update-country/{id}', [
        SaleAdminController::class,
        'updateCountry'
    ])->name('sales-admin#update-country');
    Route::post('/update-bu/{id}', [
        SaleAdminController::class,
        'updateBU'
    ])->name('sales-admin#update-bu');

    Route::post('/delete-bu/{id}', [
        SaleAdminController::class,
        'deleteBU'
    ])->name('sales-admin#delete-bu');
    Route::post('/delete-country/{id}', [
        SaleAdminController::class,
        'deleteCountry'
    ])->name('sales-admin#delete-bu');
});
