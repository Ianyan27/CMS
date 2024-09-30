<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\Owner;
use Illuminate\Http\Request;

class SaleAdminController extends Controller{
    public function index(){
        $businessUnit = BusinessUnit::all();
        $owners = Owner::all();
        return view('Sale_Admin_Page')->with([
            'owners'=>$owners, 
            'businessUnit' => $businessUnit
        ]);
    }
    public function getBUData(Request $request){
        // Validate request
        $request->validate([
            'business_unit' => 'required|string',
        ]);
        // Fetch the selected business unit
        $businessUnit = BusinessUnit::where('business_unit', $request->business_unit)->first();
        // Return the countries and BUH as JSON
        return response()->json([
            'countries' => json_decode($businessUnit->country),
            'buh' => json_decode($businessUnit->BUH),
        ]);
    }
}
