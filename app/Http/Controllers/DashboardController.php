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
        
        // --- 1. STATISTIK ORGANISASI (MASTER DATA) ---
        // Ini query cross-database ke db_master_amarin
        $countEmployees = User::count();
        $countDepartments = Department::count();
        $countCompanies = Company::count();

        // --- 2. DATA RL (Milik Sendiri / Role Based) ---
        $countDraft = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'DRAFT')->count();

        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        if ($isApprover) {
            // MODE MANAGER/DIRECTOR
            $countPending = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'PENDING')->count();
            $countApproved = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'APPROVED')->count();
            $countRejected = ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'REJECTED')->count();
            
            // Tabel: Filter yang butuh approval saya + history saya
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
            // MODE STAFF
            $countPending = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'ON_PROGRESS')->count();
            $countApproved = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'APPROVED')->count();
            $countRejected = RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'REJECTED')->count();
            
            $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)->get();
        }

        $countTotal = $countPending + $countApproved + $countRejected + $countDraft;

        // Data Chart
        $chartData = json_encode([$countDraft, $countPending, $countApproved, $countRejected]);

        return view('dashboard', compact(
            'countTotal', 'countDraft', 'countPending', 'countApproved', 'countRejected', 
            'recent_rls', 'chartData', 'isApprover',
            'countEmployees', 'countDepartments', 'countCompanies' // <-- Kirim data baru
        ));
    }
}