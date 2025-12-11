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

// Group Auth
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. REQUISITION ROUTES (URUTAN SANGAT PENTING!)

    // A. Route Khusus (HARUS DI ATAS RESOURCE)
    // Supaya "department" tidak dianggap sebagai ID surat
    Route::get('/requisitions/department', [RequisitionController::class, 'departmentActivity'])
        ->name('requisitions.department');

    Route::get('/requisitions/status/{status}', [RequisitionController::class, 'listByStatus'])
        ->name('requisitions.status');

    Route::post('/requisitions/{id}/submit-draft', [RequisitionController::class, 'submitDraft'])
        ->name('requisitions.submit-draft');

    Route::get('/requisitions/{id}/print', [RequisitionController::class, 'printPdf'])
        ->name('requisitions.print');

    Route::post('/requisitions/preview-temp', [RequisitionController::class, 'previewTemp'])
        ->name('requisitions.preview-temp');

    Route::get('/requisitions/{id}/revise', [RequisitionController::class, 'revise'])
        ->name('requisitions.revise');

    // B. Route Resource (Menangani /requisitions/{id})
    // Ini ditaruh paling bawah di grup requisition
    Route::resource('requisitions', RequisitionController::class);


    // 4. APPROVAL
    Route::post('/approval/action', [ApprovalController::class, 'action'])->name('approval.action');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // 5. SUPPLY TRACKING
    Route::post('/supply/store', [SupplyController::class, 'store'])->name('supply.store');

    // 6. API Internal
    Route::get('/api/get-departments/{company_id}', function ($company_id) {
        return \App\Models\Department::where('company_id', $company_id)->get();
    })->name('api.departments');

    Route::get('/api/get-positions/{company_id}', function ($company_id) {
        return \App\Models\Position::where('company_id', $company_id)->get();
    })->name('api.positions');

    Route::get('/dashboard/select-role/{role}', [DashboardController::class, 'selectRole'])
        ->name('dashboard.select_role');

    // 7. ADMIN GROUP
    Route::middleware(['auth', 'verified', 'can:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::get('/monitoring', [\App\Http\Controllers\Admin\GlobalMonitoringController::class, 'index'])->name('monitoring.index');
    });
});

require __DIR__.'/auth.php';
