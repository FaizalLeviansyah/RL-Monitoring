<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\SupplyController;

// Halaman Depan
Route::get('/', function () {
    return view('welcome');
});

// Group Auth (Hanya bisa diakses login)
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFILE (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. REQUISITION (CRUD Utama)
    Route::resource('requisitions', RequisitionController::class);
    // Print PDF
    Route::get('/requisitions/{id}/print', [RequisitionController::class, 'printPdf'])->name('requisitions.print');

    // 4. APPROVAL (Action Manager)
    Route::post('/approval/action', [ApprovalController::class, 'action'])->name('approval.action');

    // 5. SUPPLY TRACKING (Penerimaan Barang)
    // Kita gunakan nama 'supply.store' agar cocok dengan show.blade.php
    Route::post('/supply/store', [SupplyController::class, 'store'])->name('supply.store');

    // Route untuk Halaman List per Status (Draft, Approved, dll)
    Route::get('/requisitions/status/{status}', [RequisitionController::class, 'listByStatus'])
        ->name('requisitions.status');

    // Route Submit Draft
    Route::post('/requisitions/{id}/submit-draft', [RequisitionController::class, 'submitDraft'])
        ->name('requisitions.submit-draft');

    // Group khusus Super Admin
    Route::middleware(['auth', 'verified', 'can:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    // Master Data Lain (Company/Dept) bisa ditambah disini
    });
});

require __DIR__.'/auth.php';
