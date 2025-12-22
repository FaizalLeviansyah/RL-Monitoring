<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionLetter;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // TARGET: Semua View ('*') agar badge muncul dimanapun (Sidebar/Navbar/Mobile Menu)
        View::composer('*', function ($view) {

            // Default Values (Nol Semua)
            $counts = [
                'countPendingApprove'  => 0, // Waiting Approval
                'countWaitingDirector' => 0, // Waiting Director
                'countWaitingSupply'   => 0, // Waiting Supply
                'countRejected'        => 0, // Rejected
            ];

            // Cek apakah User Login?
            if (Auth::check()) {
                $user = Auth::user();

                // 1. Tentukan Role
                // Menggunakan optional() agar tidak error jika relasi position null
                $posName = optional($user->position)->position_name ?? '';

                $isSuperAdmin = ($posName === 'Super Admin');
                $isApprover   = in_array($posName, ['Manager', 'Director', 'Managing Director', 'Deputy Managing Director', 'General Manager']);

                // 2. Bangun Query Dasar
                $query = RequisitionLetter::query();

                // 3. Terapkan Filter Berdasarkan Role
                if ($isSuperAdmin) {
                    // Super Admin: Hitung Semua (Tanpa Filter)
                } elseif ($isApprover) {
                    // Approver: Hitung surat milik Dept dia + Surat miliknya sendiri
                    $query->where('company_id', $user->company_id);

                    // Ambil bawahan (Cross-DB Safe)
                    // Pastikan koneksi DB User benar
                    $userDb = $user->getConnection()->getDatabaseName();

                    $deptColleagues = DB::table($userDb . '.tbl_employee')
                                        ->where('department_id', $user->department_id)
                                        ->pluck('employee_id');

                    $query->whereIn('requester_id', $deptColleagues);

                } else {
                    // Staff Biasa: Hanya surat sendiri
                    $query->where('requester_id', $user->employee_id);
                }

                // 4. Hitung Status (Cloning query agar filter sebelumnya terbawa)
                // Pastikan nama kolom status_flow sesuai database Anda
                $counts['countPendingApprove']  = (clone $query)->where('status_flow', 'ON_PROGRESS')->count();
                $counts['countWaitingDirector'] = (clone $query)->where('status_flow', 'PARTIALLY_APPROVED')->count();
                $counts['countWaitingSupply']   = (clone $query)->where('status_flow', 'WAITING_SUPPLY')->count();
                $counts['countRejected']        = (clone $query)->where('status_flow', 'REJECTED')->count();
            }

            // Kirim ke View
            $view->with($counts);
        });
    }
}
