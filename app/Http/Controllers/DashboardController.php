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
    public function index()
    {
        $user = Auth::user();

        // -----------------------------------------------------------
        // 0. CEK APAKAH SUPER ADMIN?
        // -----------------------------------------------------------
        $isSuperAdmin = false;
        if ($user->position && $user->position->position_name === 'Super Admin') {
            $isSuperAdmin = true;
        }

        if ($isSuperAdmin) {
            return $this->superAdminDashboard();
        }

        // -----------------------------------------------------------
        // 1. LOGIC UNTUK USER BIASA (STAFF / MANAGER / DIREKTUR)
        // -----------------------------------------------------------
        
        // A. Statistik Organisasi (Master Data)
        $countEmployees = User::count();
        $countDepartments = Department::count();
        $countCompanies = Company::count();

        // B. Data RL (Draft Milik Sendiri)
        // PERBAIKAN: Gunakan 'status_flow'
        $countDraft = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'DRAFT')->count();

        // C. Cek Apakah Approver?
        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        if ($isApprover) {
            // === MODE MANAGER/DIREKTUR (Lihat Tugas Approval) ===
            // Note: Tabel ApprovalQueue kolomnya memang 'status' (bukan status_flow) -> SUDAH BENAR
            $countPending = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'PENDING')->count();
            $countApproved = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'APPROVED')->count();
            $countRejected = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'REJECTED')->count();
            
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

        } else {
            // === MODE STAFF (Lihat Surat Saya Saja) ===
            // PERBAIKAN: Gunakan 'status_flow' di sini
            $countPending = RequisitionLetter::where('requester_id', $user->employee_id)
                                ->where('status_flow', 'ON_PROGRESS')->count();
            
            $countApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                                ->where('status_flow', 'APPROVED')->count();
            
            $countRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                                ->where('status_flow', 'REJECTED')->count();
            
            $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
        }

        $countTotal = $countPending + $countApproved + $countRejected + $countDraft;

        // Data Chart
        $chartData = json_encode([$countDraft, $countPending, $countApproved, $countRejected]);

        return view('dashboard', compact(
            'countTotal', 'countDraft', 'countPending', 'countApproved', 'countRejected', 
            'recent_rls', 'chartData', 'isApprover',
            'countEmployees', 'countDepartments', 'countCompanies'
        ));
    }

    /**
     * DASHBOARD KHUSUS SUPER ADMIN
     */
    private function superAdminDashboard()
    {
        // 1. Statistik Global
        $totalEmployees = User::count();
        $totalRL = RequisitionLetter::count();
        
        // PERBAIKAN: Gunakan 'status_flow'
        $totalPending = RequisitionLetter::where('status_flow', 'ON_PROGRESS')->count();

        // 2. Data Chart
        $companies = Company::all();
        $labels = [];
        $series = [];

        foreach ($companies as $comp) {
            $labels[] = $comp->company_code; 
            $series[] = RequisitionLetter::where('company_id', $comp->company_id)->count();
        }

        $chartLabels = $labels;
        $chartSeries = $series;

        // 3. Aktivitas Global
        $globalActivities = RequisitionLetter::with(['company', 'requester'])
                            ->latest()
                            ->limit(10)
                            ->get();

        return view('dashboard_superadmin', compact(
            'totalEmployees', 'totalRL', 'totalPending',
            'chartLabels', 'chartSeries', 'globalActivities'
        ));
    }
}