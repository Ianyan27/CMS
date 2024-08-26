<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HubspotContactController extends Controller
{
    public function submitHubspotContacts(Request $request)
    {
        $selectedContacts = $request->input('selectedContacts');
    
        if ($selectedContacts) {
            // Process the selected contacts as needed
            // For example, you can log the selected contacts, send them to an external API, etc.
            Log::info('Selected contacts: ', $selectedContacts);
            
        } else {
            // Handle the case where no contacts were selected
            // You can return an error message or redirect with a flash message
            return redirect()->back()->with('error', 'No contacts selected.');
        }
    
        return redirect()->back()->with('success', 'Contacts submitted successfully.');
    }
    
}
