<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\ApprovalQueue;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // 1. FUNGSI UTAMA DASHBOARD
// 1. FUNGSI UTAMA DASHBOARD
public function index()
    {
        $user = Auth::user();

        // Cek Role (Safe Check)
        $isSuperAdmin = $user->position && $user->position->position_name === 'Super Admin';
        $isApprover = ($user->position && in_array($user->position->position_name, ['Manager', 'Director']));

        // Mode Switch
        $currentMode = session('active_role', ($isApprover ? 'approver' : 'requester'));

        // --- 1. STATISTICS COUNTER ---
        $stats = [
            'my_actions' => 0,
            'waiting_director' => 0,
            'waiting_supply' => 0,
            'completed' => 0,
            'rejected' => 0,
            'waiting_approval' => 0,
            'total_all' => 0,
        ];

        // QUERY BUILDER DASAR
        $queryRL = RequisitionLetter::query();

        // FILTER BASE
        if (!$isSuperAdmin) {
            if ($currentMode == 'approver' && $isApprover) {
                // Manager lihat data departemennya
                $queryRL->where('company_id', $user->company_id);

                // ACTION: Hitung Tugas Approval Saya
                $stats['my_actions'] = ApprovalQueue::where('approver_id', $user->employee_id)
                                        ->where('status', 'PENDING')->count();
            } else {
                // Requester lihat punya sendiri
                $queryRL->where('requester_id', $user->employee_id);

                // ACTION: Draft/Revisi
                $stats['my_actions'] = RequisitionLetter::where('requester_id', $user->employee_id)
                                        ->whereIn('status_flow', ['DRAFT', 'REJECTED'])->count();
            }
        } else {
            $stats['my_actions'] = 0;
        }

        // HITUNG MONITORING
        $stats['waiting_approval'] = (clone $queryRL)->where('status_flow', 'ON_PROGRESS')->count();
        $stats['waiting_director'] = (clone $queryRL)->where('status_flow', 'PARTIALLY_APPROVED')->count();
        $stats['waiting_supply']   = (clone $queryRL)->where('status_flow', 'WAITING_SUPPLY')->count();
        $stats['completed']        = (clone $queryRL)->where('status_flow', 'COMPLETED')->count();
        $stats['rejected']         = (clone $queryRL)->where('status_flow', 'REJECTED')->count();
        $stats['total_all']        = (clone $queryRL)->count();


        // === [PERBAIKAN DISINI] ===
        // LOGIKA BARU: HITUNG PRIORITY BERDASARKAN SELISIH HARI

        $allRLs = (clone $queryRL)->get(); // Ambil semua data untuk diloop

        // Inisialisasi Array Key agar tidak Undefined
        $priorityStats = [
            'Top Urgent' => 0,
            'Urgent' => 0,
            'Normal' => 0,
            'Outstanding' => 0
        ];

        foreach ($allRLs as $rl) {
            // Cek apakah ada tanggal request & required
            if ($rl->required_date && $rl->request_date) {
                $reqDate = Carbon::parse($rl->request_date);
                $dueDate = Carbon::parse($rl->required_date);

                // Jika status belum selesai dan tanggal sudah lewat -> Outstanding
                if ($dueDate->isPast() && !in_array($rl->status_flow, ['COMPLETED', 'REJECTED'])) {
                    $priorityStats['Outstanding']++;
                    continue;
                }

                // Hitung selisih hari
                $diffDays = $reqDate->diffInDays($dueDate, false);

                if ($diffDays <= 2) {
                    $priorityStats['Top Urgent']++;
                } elseif ($diffDays <= 5) {
                    $priorityStats['Urgent']++;
                } else {
                    $priorityStats['Normal']++;
                }
            } else {
                // Jika tanggal kosong, default ke Normal
                $priorityStats['Normal']++;
            }
        }
        // === [AKHIR PERBAIKAN] ===


        // --- WIDGET BARU 2: UPCOMING DEADLINES (H-7) ---
        $upcomingDeadlines = (clone $queryRL)
                                ->whereNotIn('status_flow', ['COMPLETED', 'REJECTED'])
                                ->whereNotNull('required_date')
                                ->where('required_date', '>=', now())
                                ->orderBy('required_date', 'asc')
                                ->with('requester')
                                ->limit(5)
                                ->get();

        // --- 2. RECENT ACTIVITY TABLE ---
        $recentActivities = (clone $queryRL)
                            ->with(['requester'])
                            ->latest()
                            ->limit(5)
                            ->get();

        // --- 3. DATA UNTUK GRAFIK ---
        $chartData = [
            'labels' => ['Waiting Approval', 'Waiting Director', 'Waiting Supply', 'Completed', 'Rejected'],
            'data' => [
                $stats['waiting_approval'],
                $stats['waiting_director'],
                $stats['waiting_supply'],
                $stats['completed'],
                $stats['rejected']
            ],
            'colors' => ['#f97316', '#9333ea', '#eab308', '#14b8a6', '#ef4444']
        ];

        // --- 4. DATA MASTER ---
        $masterData = [
            'employees' => User::count(),
            'departments' => Department::count(),
            'companies' => Company::count(),
        ];

        return view('dashboard', compact(
            'stats',
            'recentActivities',
            'chartData',
            'masterData',
            'currentMode',
            'isApprover',
            'priorityStats', // <-- Data ini sekarang sudah punya key 'Top Urgent'
            'upcomingDeadlines'
        ));
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
