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
    return view('aws', ['title'=> 'Monitoring AWS']);
});

Route::get('/display', [DeviceController::class, 'showDisplay']);

Route::delete('/api/delete-device/{device}', [DeviceController::class, 'deleteDevice']);


Route::get('/history', [DeviceController::class, 'showDeviceLogs'])->name('device.logs');
Route::get('/history/download', [DeviceController::class, 'downloadDeviceLogs'])->name('device.logs.download');

Route::get('/dashboard', [DeviceController::class, 'showDashboard']);
