<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/display', function () {
    return view('display');
});



Route::get('/aws', function () {
    return view('aws');
});

