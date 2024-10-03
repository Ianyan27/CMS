<?php

namespace App\Http\Controllers;

use App\Models\BU;
use App\Models\BuCountry;
use App\Models\BUH;
use App\Models\BusinessUnit;
use App\Models\Owner;
use App\Models\SaleAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleAdminController extends Controller
{
    public function index()
    {
        $businessUnit = BU::all();
        $owners = SaleAgent::all();
        return view('Sale_Admin_Page')->with([
            'owners' => $owners,
            'businessUnit' => $businessUnit
        ]);
    }
    public function getBUData(Request $request)
    {
        // Validate that the business_unit is provided as a string
        $request->validate([
            'business_unit' => 'required|string',
        ]);

        // Get the name of the Business Unit from the request
        $buName = $request->input('business_unit');

        // Find the Business Unit by its name
        $bu = BU::where('name', $buName)->first();
        Log::info('The BU Id is: ' . $bu->id);
        // If no BU is found, return an error response
        if (!$bu) {
            return response()->json(['error' => 'Business Unit not found'], 404);
        }

        // Get the corresponding BU ID
        $buId = $bu->id;
        

        // Get the BUH list associated with the BU
        $buhList = BUH::whereHas('buCountries', function ($query) use ($buId) {
            $query->where('bu_id', $buId);
        })->get();

        // Find the BuCountry records related to this BU
        $buCountries = BuCountry::with('country')
            ->where('bu_id', $buId)
            ->get();

        // Prepare the response by extracting the unique country names
        $response = [
            'countries' => $buCountries->pluck('country.name')->unique(),
            'buh' => $buhList->pluck('name'), // Get the names of the BUHs related to this BU
        ];

        // Log the response data for debugging purposes
        Log::info("Response: ", $response);

        // Return the response as JSON
        return response()->json($response);
    }
}
