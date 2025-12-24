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

        // --- [FIX: LANDING PAGE INTERCEPTION] ---
        // Jika Approver belum memilih mode, tampilkan Landing Page
        if ($isApprover && !session()->has('active_role')) {
            return view('landing');
        }

        // Jika sudah memilih, atau bukan approver, tentukan mode
        $defaultMode = $isApprover ? 'approver' : 'requester';
        $currentMode = session('active_role', $defaultMode);

        // 3. INITIALIZE STATISTICS
        $stats = [
            'my_actions' => 0,
            'waiting_director' => 0,
            'waiting_supply' => 0,
            'completed' => 0,
            'rejected' => 0,
            'waiting_approval' => 0,
            'total_all' => 0,
        ];

        // 4. BASE QUERY BUILDER
        $queryRL = RequisitionLetter::query();

        if ($isSuperAdmin) {
            // No Filter
        } elseif ($currentMode == 'approver') {

            // FILTER SCOPE DATA
            if ($isDirector) {
                // Director: All Company Data
                $queryRL->where('company_id', $user->company_id);

                // My Action: PARTIALLY_APPROVED
                $stats['my_actions'] = RequisitionLetter::where('company_id', $user->company_id)
                                        ->where('status_flow', 'PARTIALLY_APPROVED')
                                        ->count();

            } elseif ($isManager) {
                // Manager: Department Data
                $queryRL->where('company_id', $user->company_id);

                // Get Subordinates
                $userModel = new User();
                $userDb = $userModel->getConnection()->getDatabaseName();
                $deptIds = DB::connection($userModel->getConnectionName())
                            ->table($userDb . '.tbl_employee')
                            ->where('department_id', $user->department_id)
                            ->pluck('employee_id');

                $queryRL->whereIn('requester_id', $deptIds);

                // My Action: Approval Queue PENDING
                $stats['my_actions'] = ApprovalQueue::where('approver_id', $user->employee_id)
                                        ->where('status', 'PENDING')
                                        ->count();
            }

        } else {
            // Requester Mode
            $queryRL->where('requester_id', $user->employee_id);
            $stats['my_actions'] = (clone $queryRL)->whereIn('status_flow', ['DRAFT', 'REJECTED'])->count();
        }

        // 5. CALCULATE WIDGETS
        $stats['waiting_approval'] = (clone $queryRL)->where('status_flow', 'ON_PROGRESS')->count();
        $stats['waiting_director'] = (clone $queryRL)->where('status_flow', 'PARTIALLY_APPROVED')->count();
        $stats['waiting_supply']   = (clone $queryRL)->where('status_flow', 'WAITING_SUPPLY')->count();
        $stats['completed']        = (clone $queryRL)->where('status_flow', 'COMPLETED')->count();
        $stats['rejected']         = (clone $queryRL)->where('status_flow', 'REJECTED')->count();
        $stats['total_all']        = (clone $queryRL)->count();

        // 6. PRIORITY STATS
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

        // 7. EXTRAS
        $recentActivities = (clone $queryRL)->with(['requester'])->latest()->limit(5)->get();
        $upcomingDeadlines = (clone $queryRL)->whereNotIn('status_flow', ['COMPLETED', 'REJECTED'])
                                ->whereNotNull('required_date')->where('required_date', '>=', now())
                                ->orderBy('required_date', 'asc')->with('requester')->limit(5)->get();

        $chartData = [
            'labels' => ['Waiting Approval', 'Waiting Director', 'Final Approved', 'Completed', 'Rejected'],
            'data' => [$stats['waiting_approval'], $stats['waiting_director'], $stats['waiting_supply'], $stats['completed'], $stats['rejected']],
            'colors' => ['#f97316', '#9333ea', '#eab308', '#14b8a6', '#ef4444']
        ];

        $masterData = [
            'employees' => User::count(),
            'departments' => Department::count(),
            'companies' => Company::count(),
        ];

        $viewName = $isSuperAdmin ? 'dashboard' : ($currentMode == 'approver' ? 'dashboard_approver' : 'dashboard_requester');

        return view($viewName, compact(
            'stats', 'recentActivities', 'chartData', 'masterData',
            'currentMode', 'isApprover', 'priorityStats', 'upcomingDeadlines'
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
