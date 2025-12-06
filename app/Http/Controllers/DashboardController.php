<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\ApprovalQueue;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // --- 1. DATA UMUM (Milik Sendiri) ---
        // Draft selalu mengambil dari surat yang SAYA buat, apapun jabatan saya
        $countDraft = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'DRAFT')->count();

        // Cek Jabatan
        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        // --- 2. DATA STATUS LAINNYA (Tergantung Role) ---
        if ($isApprover) {
            // === MODE MANAGER/DIREKTUR (Lihat Tugas Approval) ===
            
            // Pending: Antrian yang harus SAYA approve
            $countPending = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'PENDING')->count();
            
            // Approved: Yang SUDAH saya approve
            $countApproved = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'APPROVED')->count();

            // Rejected: Yang SAYA tolak
            $countRejected = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'REJECTED')->count();
            
            // Total: Semua dokumen yang lewat meja saya (kecuali Draft saya sendiri)
            $countTotal = $countPending + $countApproved + $countRejected;

            // Tabel: Ambil surat yang ada di antrian saya
            $recent_rls = RequisitionLetter::whereHas('approvalQueues', function($q) use ($user) {
                                $q->where('approver_id', $user->employee_id);
                            })
                            ->with(['approvalQueues' => function($q) use ($user) {
                                $q->where('approver_id', $user->employee_id);
                            }])
                            ->get()
                            ->sortBy(function($rl) {
                                // Sortir: Yang PENDING taruh paling atas
                                return $rl->approvalQueues->first()->status === 'PENDING' ? 0 : 1;
                            });

        } else {
            // === MODE STAFF (Lihat Surat Saya) ===
            
            $countPending = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->where('status_flow', 'ON_PROGRESS')->count();
            
            $countApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->where('status_flow', 'APPROVED')->count();
            
            $countRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->where('status_flow', 'REJECTED')->count();

            $countTotal = $countDraft + $countPending + $countApproved + $countRejected;

            $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
        }

        // --- 3. DATA UNTUK CHART (JSON) ---
        // Urutan: [Draft, Pending, Approved, Rejected]
        $chartData = json_encode([$countDraft, $countPending, $countApproved, $countRejected]);

        return view('dashboard', compact(
            'countTotal', 'countDraft', 'countPending', 'countApproved', 'countRejected', 
            'recent_rls', 'chartData', 'isApprover'
        ));
    }
}