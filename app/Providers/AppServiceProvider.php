<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\RequisitionLetter;
use App\Models\ApprovalQueue;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gate untuk Super Admin (Sesuai Middleware can:super_admin)
        Gate::define('super_admin', function ($user) {
            // Pastikan user punya posisi dan namanya 'Super Admin'
            return $user->position && $user->position->position_name === 'Super Admin';
        });

        // LOGIC SMART COUNTER (GLOBAL)
        View::composer('*', function ($view) {

            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->employee_id;

                // --- 1. COUNTER KHUSUS REQUESTER (MY REQUESTS) ---
                // Menghitung dokumen milik user sendiri

                // Draft
                $countDraft = RequisitionLetter::where('requester_id', $userId)
                                ->where('status_flow', 'DRAFT')
                                ->count();

                // On Progress (Sedang di Manager)
                $countMyOnProgress = RequisitionLetter::where('requester_id', $userId)
                                ->where('status_flow', 'ON_PROGRESS')
                                ->count();

                // Waiting Director (Sedang di Direktur) - PENTING untuk Requester
                $countMyWaitingDirector = RequisitionLetter::where('requester_id', $userId)
                                ->where('status_flow', 'PARTIALLY_APPROVED')
                                ->count();

                // Rejected (Butuh Revisi)
                $countRejected = RequisitionLetter::where('requester_id', $userId)
                                ->where('status_flow', 'REJECTED')
                                ->count();


                // --- 2. COUNTER KHUSUS APPROVER (MONITORING/TASKS) ---
                // Menghitung antrian pekerjaan (To-Do List)

                // Antrian Approval Saya (Pending Tasks)
                $countPendingTask = ApprovalQueue::where('approver_id', $userId)
                                        ->where('status', 'PENDING')
                                        ->count();

                // Global Monitoring (Untuk Dashboard)
                $countGlobalOnProgress = RequisitionLetter::where('status_flow', 'ON_PROGRESS')->count();
                $countGlobalWaitingDirector = RequisitionLetter::where('status_flow', 'PARTIALLY_APPROVED')->count();
                $countGlobalWaitingSupply = RequisitionLetter::where('status_flow', 'WAITING_SUPPLY')->count();


                // Share variabel ke semua View dengan nama yang SPESIFIK
                $view->with([
                    // Requester Vars
                    'countDraft' => $countDraft,
                    'countMyOnProgress' => $countMyOnProgress,       // GANTI VARIABEL INI
                    'countMyWaitingDirector' => $countMyWaitingDirector,
                    'countRejected' => $countRejected,

                    // Approver Vars
                    'countPendingTask' => $countPendingTask,         // GANTI VARIABEL INI
                    'countGlobalOnProgress' => $countGlobalOnProgress,
                    'countGlobalWaitingDirector' => $countGlobalWaitingDirector,
                    'countGlobalWaitingSupply' => $countGlobalWaitingSupply,
                ]);
            }
        });
    }
}
