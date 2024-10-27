<?php

namespace App\Http\Controllers;

use App\Models\BU;
use App\Models\BuCountry;
use App\Models\BuCountryBUH;
use App\Models\BUH;
use App\Models\BusinessUnit;
use App\Models\Country;
use App\Models\Owner;
use App\Models\SaleAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        Log::info($user);
        $bus = BU::paginate(10); // adjust the pagination limit as needed
        $country = Country::all();
        $countryCount = $country->count();
        $countries = Country::paginate(10); // adjust the pagination limit as needed
        return view('BU_Country')->with([
            'user' => $user,
            'bus' => $bus,
            'country' => $country,
            'countries' => $countries,
            'countryCount' => $countryCount
        ]);
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

        // Retrieve BuCountry records related to this BU
        $buCountries = BuCountry::with('country')
            ->where('bu_id', $buId)
            ->get();

        // Extract unique country names related to this BU
        $countryNames = $buCountries->pluck('country.name')->unique()->values()->toArray();

        // Find BUHs associated with this BU through BuCountry relationship
        // Assuming BUH is related to BuCountry through `bu_country_id` or similar
        $buhList = BUH::whereIn('id', function ($query) use ($buId) {
            $query->select('buh_id')
                ->from('bu_country_buh') // Assuming a pivot table `bu_country_buh`
                ->where('country_id', $buId);
        })->get();

        // Prepare the response by extracting the unique country names and BUH names
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
        // Validate the country name, ensuring it's unique in the `countries` table
        $request->validate([
            'country-name' => 'required|string|max:255|unique:country,name',
        ], [
            'country-name.unique' => 'The country already exists.', // Custom error message for existing country
        ]);

        // If validation passes, create a new Country instance and save it
        $country = new Country();
        $country->name = $request->input('country-name');
        $country->save();

        return redirect()->back()->with('success', 'Country added successfully!');
    }


    public function saveBU(Request $request)
    {
        $request->validate([
            'bu-name' => 'required',
            'country' => 'required|array|min:1',  // Ensure at least one country is selected
        ]);

        // Create the new Business Unit
        $bu = new BU();
        $bu->name = $request->input('bu-name');
        $bu->save();

        // Save each selected country for this BU
        $countryIds = $request->input('country');  // This is now an array of selected country IDs
        Log::info($countryIds);
        foreach ($countryIds as $countryId) {
            $buCountry = new BuCountry();
            $buCountry->bu_id = $bu->id;
            $buCountry->country_id = $countryId;
            $buCountry->save();
        }

        return redirect()->back()->with('success', 'Business Unit added successfully with selected countries!');
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

        return redirect()->back()->with('success', 'Business Unit updated successfully!');
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

        return redirect()->back()->with('success', 'Country updated successfully!');
    }

    public function deleteBU($id)
    {
        $bu = BU::find($id);

        if (!$bu) {
            // Handle the case where the business unit is not found
            return redirect()->back()->withErrors('Business Unit not found');
        }

        $bu->delete();

        return redirect()->back()->with('bu-success', 'Business Unit deleted successfully!');
    }

    public function deleteCountry($id)
    {
        $country = Country::find($id);

        if (!$country) {
            // Handle the case where the country is not found
            return redirect()->back()->withErrors('Country not found');
        }

        $country->delete();

        return redirect()->back()->with('success', 'Country deleted successfully!');
    }
}
