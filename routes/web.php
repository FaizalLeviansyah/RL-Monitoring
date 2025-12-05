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

// Route Approval Simple
Route::post('/approval/action', [ApprovalController::class, 'action'])->name('approval.action');

// --- TAMBAHAN BARU (SOLUSI ERROR PDF) ---
    Route::get('/requisitions/{id}/print', [RequisitionController::class, 'printPdf'])->name('requisitions.print');

    // --- TAMBAHAN BARU (SOLUSI APPROVAL TANPA POPUP) ---
    Route::post('/approval/action', [ApprovalController::class, 'action'])->name('approval.action');

require __DIR__.'/auth.php';
