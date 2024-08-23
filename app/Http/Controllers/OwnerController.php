<?php

namespace App\Http\Controllers;
use App\Models\Owner;

class OwnerController extends Controller{

    public function viewOwner(){
        $owner = Owner::paginate(10);
        return view('Sale_Agent_Page', [
            'owner'=>$owner
        ]);
    }
}