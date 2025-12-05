<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\ApprovalController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route of Requisition
Route::resource('requisitions', RequisitionController::class);

// Route Approval (OTP)
Route::post('/approval/request-otp', [ApprovalController::class, 'requestOtp'])->name('approval.request-otp');
Route::post('/approval/verify-otp', [ApprovalController::class, 'verifyOtp'])->name('approval.verify-otp');

require __DIR__.'/auth.php';
