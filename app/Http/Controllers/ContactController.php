<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{

    public function index()
    {
        // Get contacts from model
        $contacts = Contact::paginate(10);

        // Pass data to view
        return view('Contact_Listing', ['contacts' => $contacts]);
    }
}
