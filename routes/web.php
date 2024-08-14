<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user_list_dashboard', function(){
    return view('User_List_Page');
});