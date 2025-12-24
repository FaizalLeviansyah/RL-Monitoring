<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <--- WAJIB DITAMBAHKAN UNTUK PERMISSION
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionLetter;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // =================================================================
        // 1. GLOBAL GATE: SUPER ADMIN BYPASS (SOLUSI ERROR 403)
        // =================================================================
        Gate::before(function ($user, $ability) {
            // Pastikan relasi position diload, lalu cek namanya
            // Kita gunakan optional() untuk keamanan jika user tidak punya jabatan
            if (optional($user->position)->position_name === 'Super Admin') {
                return true; // Izinkan SEMUA akses
            }
        });

        // =================================================================
        // 2. VIEW COMPOSER: BADGE COUNTER DI SIDEBAR
        // =================================================================
        View::composer('*', function ($view) {

            // Default Values (Nol Semua) agar tidak error saat belum login
            $counts = [
                'countPendingApprove'  => 0, // Waiting Approval
                'countWaitingDirector' => 0, // Waiting Director
                'countWaitingSupply'   => 0, // Waiting Supply
                'countRejected'        => 0, // Rejected
            ];

            // Cek apakah User Login?
            if (Auth::check()) {
                $user = Auth::user();

                // Ambil Nama Jabatan (Safe Mode)
                $posName = optional($user->position)->position_name ?? '';

                $isSuperAdmin = ($posName === 'Super Admin');
                // Daftar jabatan yang dianggap sebagai Approver
                $isApprover   = in_array($posName, ['Manager', 'Director', 'Managing Director', 'Deputy Managing Director', 'General Manager']);

                // Bangun Query Dasar
                $query = RequisitionLetter::query();

                // Terapkan Filter Query Berdasarkan Role
                if ($isSuperAdmin) {
                    // Super Admin: Hitung Semua (Tanpa Filter)
                    // Tidak perlu where, ambil semua data global
                } elseif ($isApprover) {
                    // Approver: Hitung surat milik Dept dia + Surat miliknya sendiri
                    $query->where('company_id', $user->company_id);

                    // Ambil daftar bawahan satu departemen dari DB Master (Cross-DB Safe)
                    // Kita ambil nama database dari koneksi model User agar dinamis
                    $userDb = $user->getConnection()->getDatabaseName();

                    $deptColleagues = DB::table($userDb . '.tbl_employee')
                                        ->where('department_id', $user->department_id)
                                        ->pluck('employee_id');

                    // Filter: Requester harus ada di daftar kolega departemen
                    $query->whereIn('requester_id', $deptColleagues);

                } else {
                    // Staff Biasa: Hanya menghitung surat miliknya sendiri
                    $query->where('requester_id', $user->employee_id);
                }

                // Hitung Status (Cloning query agar filter sebelumnya terbawa)
                // Pastikan value 'status_flow' ini SAMA PERSIS dengan enum di database
                $counts['countPendingApprove']  = (clone $query)->where('status_flow', 'ON_PROGRESS')->count();
                $counts['countWaitingDirector'] = (clone $query)->where('status_flow', 'PARTIALLY_APPROVED')->count();
                $counts['countWaitingSupply']   = (clone $query)->where('status_flow', 'WAITING_SUPPLY')->count();
                $counts['countRejected']        = (clone $query)->where('status_flow', 'REJECTED')->count();
            }

            // Kirim variabel $counts ke semua View
            $view->with($counts);
        });
    }
}
