<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

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

Route::get('/aws', function () {
    return view('aws');
})->middleware(['auth', 'verified'])->name('aws');

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