<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\IframeController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard',[DeviceController::class, 'showDashboard'], function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/aws', [IframeController::class, 'index'])->middleware(['auth', 'verified'])->name('aws');
Route::post('/aws', [IframeController::class, 'store'])->middleware(['auth', 'verified'])->name('addIframe');
Route::delete('/api/delete-iframe/{iframe}', [IframeController::class, 'destroy'])->middleware(['auth', 'verified']);

Route::get('/display', [DeviceController::class, 'showDisplay'], function () {
    return view('display');
})->middleware(['auth', 'verified'])->name('display');

Route::get('/history', [DeviceController::class, 'showDeviceLogs'], function () {
    return view('history');
})->middleware(['auth', 'verified'])->name('device.logs');

Route::get('/history/download', [DeviceController::class, 'downloadDeviceLogs']
)->middleware(['auth', 'verified'])->name('device.logs.download');

Route::delete('/api/delete-device/{device}', [DeviceController::class, 'deleteDevice']);

require __DIR__.'/auth.php';