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
public function index()
    {
        $user = Auth::user();
        $userPos = $user->position->position_name ?? '';
        
        // 1. ROLE DEFINITION
        $approverRoles = [
            'Manager',
            'Director',
            'Managing Director',
            'Deputy Managing Director',
            'General Manager',
            'President Director'
        ];

        $isSuperAdmin = ($userPos === 'Super Admin');
        $isDirector = in_array($userPos, ['Director', 'Managing Director', 'Deputy Managing Director', 'General Manager', 'President Director']);
        $isManager = ($userPos === 'Manager');
        $isApprover = in_array($userPos, $approverRoles);

        // 2. MODE DETERMINATION
        if ($isApprover && !session()->has('active_role')) {
            return view('landing');
        }

        if ($isSuperAdmin) {
            $currentMode = 'admin';
        } elseif ($isApprover) {
            $currentMode = session('active_role', 'approver');
        } else {
            $currentMode = 'requester';
        }

        // 3. BASE QUERY BUILDER
        $queryRL = RequisitionLetter::query();
        $myActionsCount = 0;
        $waitingConfirmation = collect();

        if ($isSuperAdmin) {
            // No Filter
        } elseif ($currentMode == 'approver') {
            if ($isDirector) {
                $queryRL->where('company_id', $user->company_id);
                $myActionsCount = RequisitionLetter::where('company_id', $user->company_id)
                                    ->where('status_flow', 'PARTIALLY_APPROVED')->count();
            } elseif ($isManager) {
                $queryRL->where('company_id', $user->company_id);
                $deptIds = DB::table('tbl_employee')
                            ->where('department_id', $user->department_id)
                            ->pluck('employee_id');
                $queryRL->whereIn('requester_id', $deptIds);
                $myActionsCount = ApprovalQueue::where('approver_id', $user->employee_id)
                                    ->where('status', 'PENDING')->count();
            }
        } else {
            // Requester Mode
            $queryRL->where('requester_id', $user->employee_id);
            $myActionsCount = (clone $queryRL)->whereIn('status_flow', ['DRAFT', 'REJECTED'])->count();
            $waitingConfirmation = (clone $queryRL)->where('status_flow', 'APPROVED')->latest()->get();
        }

        // 4. CALCULATE STATISTICS (Single Source of Truth)
        // Kita clone agar query utama tidak terganggu
        $stats = [
            'draft'            => (clone $queryRL)->where('status_flow', 'DRAFT')->count(),
            'waiting_approval' => (clone $queryRL)->where('status_flow', 'ON_PROGRESS')->count(),
            'waiting_director' => (clone $queryRL)->where('status_flow', 'PARTIALLY_APPROVED')->count(),
            'waiting_supply'   => (clone $queryRL)->where('status_flow', 'WAITING_SUPPLY')->count(),
            'approved'         => (clone $queryRL)->where('status_flow', 'APPROVED')->count(), // [PENTING] Status Sedang Dikirim
            'completed'        => (clone $queryRL)->where('status_flow', 'COMPLETED')->count(),
            'rejected'         => (clone $queryRL)->where('status_flow', 'REJECTED')->count(),
            'total_all'        => (clone $queryRL)->count(),
            'my_actions'       => $myActionsCount
        ];

        // 5. PRIORITY & DEADLINE ANALYSIS
        $allRLs = (clone $queryRL)->get();
        $priorityStats = ['Top Urgent' => 0, 'Urgent' => 0, 'Normal' => 0, 'Outstanding' => 0];

        foreach ($allRLs as $rl) {
            if ($rl->required_date && $rl->request_date) {
                $dueDate = Carbon::parse($rl->required_date);
                if ($dueDate->isPast() && !in_array($rl->status_flow, ['COMPLETED', 'REJECTED'])) {
                    $priorityStats['Outstanding']++;
                    continue;
                }
                $diff = Carbon::parse($rl->request_date)->diffInDays($dueDate, false);
                if ($diff <= 2) $priorityStats['Top Urgent']++;
                elseif ($diff <= 5) $priorityStats['Urgent']++;
                else $priorityStats['Normal']++;
            } else {
                $priorityStats['Normal']++;
            }
        }

        // 6. EXTRA DATA
        $recentActivities = (clone $queryRL)->with(['requester'])->latest()->limit(5)->get();
        
        $upcomingDeadlines = (clone $queryRL)
            ->whereNotIn('status_flow', ['COMPLETED', 'REJECTED'])
            ->whereNotNull('required_date')
            ->where('required_date', '>=', now())
            ->orderBy('required_date', 'asc')
            ->with('requester')
            ->limit(5)
            ->get();

        // 7. CHART DATA (FIX: Menambahkan 'approved' ke dalam dataset)
        $chartData = [
            'labels' => ['Manager Review', 'Director Review', 'Procurement', 'In Delivery', 'Completed', 'Rejected'],
            'data' => [
                $stats['waiting_approval'], 
                $stats['waiting_director'], 
                $stats['waiting_supply'], 
                $stats['approved'], // <--- DATA INI YANG SEBELUMNYA HILANG
                $stats['completed'], 
                $stats['rejected']
            ],
            // Warna: Orange, Purple, Yellow, Blue, Teal, Red
            'colors' => ['#f97316', '#9333ea', '#eab308', '#3b82f6', '#14b8a6', '#ef4444']
        ];

        // 8. MASTER DATA
        $masterData = [
            'employees' => User::count(),
            'departments' => Department::count(),
            'companies' => Company::count(),
        ];

        $viewName = $isSuperAdmin ? 'dashboard' : ($currentMode == 'approver' ? 'dashboard_approver' : 'dashboard_requester');

        return view($viewName, compact(
            'stats', 
            'recentActivities', 
            'chartData', 
            'masterData',
            'currentMode', 
            'isApprover', 
            'priorityStats', 
            'upcomingDeadlines', 
            'waitingConfirmation'
        ));
    }

    public function selectRole($role)
    {
        if ($role === 'reset') {
            session()->forget('active_role');
            return redirect()->route('dashboard');
        }
        session(['active_role' => $role]);
        return redirect()->route('dashboard');
    }
}