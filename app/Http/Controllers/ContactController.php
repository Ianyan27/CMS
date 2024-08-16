<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{

    public function index()
    {
        // Read the JSON file
        $json = Storage::get('contact.json');
        $contacts = json_decode($json, true);

        // Pass data to view
        return view('Contact_Listing', ['contacts' => $contacts]);
    }
}
