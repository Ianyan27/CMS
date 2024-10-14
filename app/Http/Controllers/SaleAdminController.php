<?php

namespace App\Http\Controllers;

use App\Models\BU;
use App\Models\BuCountry;
use App\Models\BUH;
use App\Models\BusinessUnit;
use App\Models\Country;
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

    // Check the BU and Country list
    public function buCountry()
    {
        $bus = BU::paginate(10); // adjust the pagination limit as needed
        $countries = Country::paginate(10); // adjust the pagination limit as needed

        return view('BU_Country', compact('bus', 'countries'));
    }

    public function getBUData(Request $request)
    {
        // Validate that the business_unit is provided as a string
        $request->validate([
            'business_unit' => 'required|string',
        ]);

        // Get the name of the Business Unit from the request
        $buName = $request->get('business_unit');

        // Find the Business Unit by its name
        $bu = BU::where('name', $buName)->first();

        // If no BU is found, return an error response
        if (!$bu) {
            return response()->json(['error' => 'Business Unit not found'], 404);
        }

        // Get the corresponding BU ID
        $buId = $bu->id;

        // Find the BuCountry records related to this BU
        $buCountries = BuCountry::with('country')
            ->where('bu_id', $buId)
            ->get();

        $countryNames = $buCountries->pluck('country.name')->unique()->values()->toArray();
        Log::info("Countries associated with BU [{$buName}]: ", ['countries' => $countryNames]);

        $buhList = BUH::whereHas('buCountries', function ($query) use ($buId) {
            $query->where('bu_id', $buId);
        })->get();


        // Prepare the response by extracting the unique country names
        $response = [
            'countries' => $countryNames,
            'buh' => $buhList->pluck('name'), // Get the names of the BUHs related to this BU
        ];

        // Return the response as JSON
        return response()->json($response);
    }

    public function getBUHByCountry(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'country' => 'required|string',
            'business_unit' => 'required|string',
        ]);

        // Get country and business unit from request
        $countryName = $request->input('country');
        $businessUnitName = $request->input('business_unit');

        // Find the corresponding BU
        $bu = BU::where('name', $businessUnitName)->first();
        if (!$bu) {
            return response()->json(['error' => 'Business Unit not found'], 404);
        }

        // Find the country
        $country = Country::where('name', $countryName)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Retrieve BUHs associated with this BU and country
        $buhList = BUH::whereHas('buCountries', function ($query) use ($bu, $country) {
            $query->where('bu_id', $bu->id)
                ->where('country_id', $country->id);
        })->get()->unique('id'); // Ensure uniqueness based on BUH ID

        // Check if any BUHs were found
        if ($buhList->isEmpty()) {
            return response()->json(['error' => 'No BUH found for the specified country and business unit'], 404);
        }

        // Return the list of unique BUHs as JSON
        return response()->json(['buh' => $buhList]);
    }

    public function saveCountry(Request $request)
    {
        $request->validate([
            'country-name' => 'required',
        ]);

        $country = new Country();
        $country->name = $request->input('country-name');
        $country->save();

        return redirect()->back()->with('country-success', 'Country added successfully!');
    }

    public function saveBU(Request $request)
    {
        $request->validate([
            'bu-name' => 'required',
        ]);

        $bu = new BU();
        $bu->name = $request->input('bu-name');
        $bu->save();

        return redirect()->back()->with('bu-success', 'Business Unit added successfully!');
    }

    public function updateBU(Request $request, $id)
    {
        $request->validate([
            'bu-name' => 'required',
        ]);

        $bu = BU::find($id);

        if (!$bu) {
            // Handle the case where the business unit is not found
            return redirect()->back()->withErrors('Business Unit not found');
        }

        Log::info("bu name: " . $request->input('bu-name'));
        $bu->name = $request->input('bu-name');
        $bu->save();

        return redirect()->back()->with('bu-success', 'Business Unit updated successfully!');
    }

    public function updateCountry(Request $request, $id)
    {
        $request->validate([
            'country-name' => 'required',
        ]);

        $country = Country::find($id);

        if (!$country) {
            // Handle the case where the country is not found
            return redirect()->back()->withErrors('Country not found');
        }

        $country->name = $request->input('country-name');
        $country->save();

        return redirect()->back()->with('country-success', 'Country updated successfully!');
    }
}
