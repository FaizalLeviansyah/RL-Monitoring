<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\ApprovalQueue;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // 1. FUNGSI UTAMA DASHBOARD
    public function index()
    {
        $user = Auth::user();

        // A. CEK SUPER ADMIN
        if ($user->position && $user->position->position_name === 'Super Admin') {
            return $this->superAdminDashboard();
        }

        // B. CEK STAFF BIASA (Langsung Masuk Dashboard Requester)
        // Definisi Staff: Tidak punya jabatan Manager/Director
        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        if (!$isApprover) {
            // Staff dipaksa jadi Requester
            return $this->requesterDashboard($user);
        }

        // C. LOGIK MANAGER/DIRECTOR (Landing Page Selection)
        // Cek apakah user sudah memilih peran di sesi ini?
        $activeRole = session('active_role');

        if (!$activeRole) {
            // Jika belum milih, lempar ke Halaman Pilihan (Landing Page)
            return view('role_selection');
        }

        // Jika sudah milih, arahkan sesuai pilihan
        if ($activeRole === 'approver') {
            return $this->approverDashboard($user);
        } else {
            return $this->requesterDashboard($user);
        }
    }

    // 2. FUNGSI MENYIMPAN PILIHAN PERAN
    public function selectRole($role)
        {
            if ($role === 'reset') {
                session()->forget('active_role'); // Hapus sesi
                return redirect()->route('dashboard'); // Akan otomatis diarahkan ke Landing Page
            }

            session(['active_role' => $role]);
            return redirect()->route('dashboard');
        }

    // --- PRIVATE METHODS UNTUK MEMISAHKAN LOGIC VIEW ---

    private function requesterDashboard($user)
    {
        $viewType = 'Requester';

        // Data Statistik Requester (Milik Sendiri)
        $countDraft = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'DRAFT')->count();
        $countPending = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'ON_PROGRESS')->count();
        $countApproved = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'APPROVED')->count();
        $countRejected = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'REJECTED')->count();
        $countTotal = $countDraft + $countPending + $countApproved + $countRejected;

        // Tabel: List Surat Saya
        $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->orderBy('created_at', 'desc')->limit(10)->get();

        $chartData = json_encode([$countDraft, $countPending, $countApproved, $countRejected]);

        // Data Master (Untuk Info Header)
        $countEmployees = User::count();
        $countDepartments = Department::count();
        $countCompanies = Company::count();

        // Kita kirim variable $isApprover = true/false agar sidebar tetap muncul menu sesuai hak akses asli
        // Tapi viewType memberi tahu sedang mode apa
        $isApprover = in_array($user->position->position_name ?? '', ['Manager', 'Director']);

        return view('dashboard', compact(
            'viewType', 'isApprover',
            'countTotal', 'countDraft', 'countPending', 'countApproved', 'countRejected',
            'recent_rls', 'chartData',
            'countEmployees', 'countDepartments', 'countCompanies'
        ));
    }

    private function approverDashboard($user)
    {
        $viewType = 'Approver';

        // Data Statistik Approver (Tugas Approval)
        $countDraft = 0; // Approver tidak melihat draft orang lain
        $countPending = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'PENDING')->count();
        $countApproved = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'APPROVED')->count();
        $countRejected = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'REJECTED')->count();
        $countTotal = $countPending + $countApproved + $countRejected;

        // Tabel: List Antrian Approval Saya
        $recent_rls = RequisitionLetter::whereHas('approvalQueues', function($q) use ($user) {
                            $q->where('approver_id', $user->employee_id);
                        })
                        ->with(['approvalQueues' => function($q) use ($user) {
                            $q->where('approver_id', $user->employee_id);
                        }])
                        ->get()
                        ->sortBy(function($rl) {
                            return $rl->approvalQueues->first()->status === 'PENDING' ? 0 : 1;
                        });

        $chartData = json_encode([0, $countPending, $countApproved, $countRejected]);

        $countEmployees = User::count();
        $countDepartments = Department::count();
        $countCompanies = Company::count();
        $isApprover = true;

        return view('dashboard', compact(
            'viewType', 'isApprover',
            'countTotal', 'countDraft', 'countPending', 'countApproved', 'countRejected',
            'recent_rls', 'chartData',
            'countEmployees', 'countDepartments', 'countCompanies'
        ));
    }

    private function superAdminDashboard()
    {
        // (Copy logika Super Admin dari chat sebelumnya kesini)
        // ... (Singkatnya sama seperti yang sudah jalan) ...
        $totalEmployees = User::count();
        $totalRL = RequisitionLetter::count();
        $totalPending = RequisitionLetter::where('status_flow', 'ON_PROGRESS')->count();

        $companies = Company::all();
        $labels = []; $series = [];
        foreach ($companies as $comp) {
            $labels[] = $comp->company_code;
            $series[] = RequisitionLetter::where('company_id', $comp->company_id)->count();
        }
        $chartLabels = $labels; $chartSeries = $series;

        $globalActivities = RequisitionLetter::with(['company', 'requester'])->latest()->limit(10)->get();

        return view('dashboard_superadmin', compact('totalEmployees', 'totalRL', 'totalPending', 'chartLabels', 'chartSeries', 'globalActivities'));
    }
}
