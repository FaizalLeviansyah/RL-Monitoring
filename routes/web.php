<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GlobalMonitoringController;
use App\Http\Controllers\Admin\MasterItemController;

// Halaman Depan
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD & ROLE SELECTION ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/select-role/{role}', [DashboardController::class, 'selectRole'])
        ->name('dashboard.select_role');

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- REQUISITION ROUTES ---
    // Custom Routes harus diatas Resource Controller
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

    Route::post('/requisitions/{id}/upload-partial', [RequisitionController::class, 'uploadPartial'])
        ->name('requisitions.upload_partial');
    Route::post('/requisitions/{id}/upload-final', [RequisitionController::class, 'uploadFinal'])
        ->name('requisitions.upload_final');
    Route::post('/requisitions/{id}/upload-evidence', [RequisitionController::class, 'uploadEvidence'])
        ->name('requisitions.upload_evidence');

    Route::resource('requisitions', RequisitionController::class);

    // --- APPROVAL ROUTES ---
    Route::post('/approval/action', [ApprovalController::class, 'action'])->name('approval.action');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // --- SUPPLY ROUTES ---
    Route::post('/supply/store', [SupplyController::class, 'store'])->name('supply.store');

    // --- API HELPER (AJAX) ---
    Route::get('/api/get-departments/{company_id}', function ($company_id) {
        return \App\Models\Department::where('company_id', $company_id)->get();
    })->name('api.departments');

    Route::get('/api/get-positions/{company_id}', function ($company_id) {
        return \App\Models\Position::where('company_id', $company_id)->get();
    })->name('api.positions');

    // --- ADMIN ROUTES (SUPER ADMIN ONLY) ---
    Route::middleware(['can:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('master-items', MasterItemController::class);
        Route::get('/monitoring', [GlobalMonitoringController::class, 'index'])->name('monitoring.index');
    });

    // --- TAMBAHKAN ROUTE KHUSUS INI ---

    // 1. Route untuk Print PDF
    Route::get('/requisitions/{id}/print', [RequisitionController::class, 'print'])->name('requisitions.print');

    // 2. Route untuk Approve
    Route::post('/requisitions/{id}/approve', [RequisitionController::class, 'approve'])->name('requisitions.approve');

    // 3. Route untuk Reject (Ini yang bikin error tadi)
    Route::post('/requisitions/{id}/reject', [RequisitionController::class, 'reject'])->name('requisitions.reject');

    // 4. Route Filter Status (Menu Sidebar: Waiting, Rejected, dll)
    Route::get('/requisitions/status/{status}', [RequisitionController::class, 'listByStatus'])->name('requisitions.status');

    // 5. Route Department Activity
    Route::get('/department-activity', [RequisitionController::class, 'departmentActivity'])->name('requisitions.department');

    // Route untuk Requester melakukan Submit
    Route::post('/requisitions/{id}/submit', [RequisitionController::class, 'submit'])->name('requisitions.submit');
});

// DEBUG ROUTE (Opsional, bisa dihapus saat production)
Route::get('/cek-db', function () {
    $dbName = DB::connection()->getDatabaseName();
    $hasColumn = Schema::hasColumn('requisition_letters', 'attachment_partial');
    return [
        'Database' => $dbName,
        'Tabel requisition_letters' => Schema::hasTable('requisition_letters') ? 'YA' : 'TIDAK',
        'Kolom attachment_partial' => $hasColumn ? 'YA' : 'TIDAK',
    ];
});

require __DIR__.'/auth.php';
