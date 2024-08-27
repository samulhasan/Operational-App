<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::get('/', function () {
    return view('welcome', ['title'=> 'Welcome Page']);
});

Route::get('/report', function () {
    return view('report', ['title'=> 'Report']);
});

Route::get('/history', function () {
    return view('history', ['title'=> 'History']);
});

Route::get('/aws', function () {
    return view('aws', ['title'=> 'AWS Center']);
});

Route::get('/display', [DeviceController::class, 'showDisplay']);
