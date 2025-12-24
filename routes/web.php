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

    // ====================================================
    // --- REQUISITION ROUTES (CLEANED & FIXED) ---
    // ====================================================

    // 1. Action Routes (Tombol-tombol)
    Route::post('/requisitions/preview', [RequisitionController::class, 'previewTemp'])->name('requisitions.preview'); // FIX: Preview Modal
    Route::post('/requisitions/{id}/submit', [RequisitionController::class, 'submit'])->name('requisitions.submit');   // FIX: Submit Trigger
    Route::get('/requisitions/{id}/print', [RequisitionController::class, 'print'])->name('requisitions.print');       // FIX: Print PDF Filename Safe
    Route::get('/requisitions/{id}/revise', [RequisitionController::class, 'revise'])->name('requisitions.revise');

    // 2. Approval Routes (Action oleh Manager/Direktur)
    Route::post('/requisitions/{id}/approve', [RequisitionController::class, 'approve'])->name('requisitions.approve');
    Route::post('/requisitions/{id}/reject', [RequisitionController::class, 'reject'])->name('requisitions.reject');

    // 3. Filter & Menu Routes
    Route::get('/requisitions/department', [RequisitionController::class, 'departmentActivity'])->name('requisitions.department');
    Route::get('/requisitions/status/{status}', [RequisitionController::class, 'listByStatus'])->name('requisitions.status');

    // 4. Upload Routes (Support Files)
    Route::post('/requisitions/{id}/upload-partial', [RequisitionController::class, 'uploadPartial'])->name('requisitions.upload_partial');
    Route::post('/requisitions/{id}/upload-final', [RequisitionController::class, 'uploadFinal'])->name('requisitions.upload_final');
    Route::post('/requisitions/{id}/upload-evidence', [RequisitionController::class, 'uploadEvidence'])->name('requisitions.upload_evidence');

    // 5. RESOURCE CONTROLLER (Harus paling bawah agar ID tidak bentrok dengan slug lain)
    // Menangani: index, create, store, show, edit, update, destroy
    Route::resource('requisitions', RequisitionController::class);


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

});

// DEBUG ROUTE (Boleh dihapus nanti)
Route::get('/cek-db', function () {
    $dbName = DB::connection()->getDatabaseName();
    return [
        'Database' => $dbName,
        'Status' => 'Connected'
    ];
});

require __DIR__.'/auth.php';
