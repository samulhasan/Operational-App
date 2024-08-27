<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/update-screenshot', [DeviceController::class, 'updateScreenshot']);
Route::get('/get-latest-screenshot', [DeviceController::class, 'getLatestScreenshot']);