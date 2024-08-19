<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactsImportController;
use App\Models\Contact;
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

Route::get("/contact-listing", [ContactController::class, 'contacts']);
Route::get('/view_contact/{contact_pid}', [ContactController::class, 'viewContact'])->name('contact#view');
Route::get('/edit_contact/{contact_pid}', [ContactController::class, 'edit_contact'])->name('contact#edit');
Route::post('/save_contact/{contact_pid}', [ContactController::class, 'saveContact'])->name('contact#save_edit');


Route::get('/importcopy', function () {
    return view('csv_import_form');
});



Route::post('/import', [ContactsImportController::class, 'import'])->name('import');


Route::get('/editcontactdetail', function () {
    return view('Edit_Contact_Detail_Page');
});
