<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\ApprovalQueue; // <--- Tambahkan Model Ini
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cek Jabatan User (Berdasarkan nama posisi)
        // Asumsi: Staff tidak punya kata 'Manager' atau 'Director' di jabatannya
        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        // --- LOGIC DATA ---

        if ($isApprover) {
            // === LOGIC UNTUK MANAGER/DIREKTUR ===

            // 1. Total: Hitung antrian approval milik saya (yang PENDING)
            $myPending = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'PENDING')
                            ->count();

            // 2. Total Permintaan: Semua surat yang masuk ke meja saya (Pending/Approved/Rejected)
            $myTotal = ApprovalQueue::where('approver_id', $user->employee_id)->count();

            // 3. Disetujui: Yang sudah saya Approve
            $myApproved = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'APPROVED')
                            ->count();

            // 4. Ditolak: Yang saya Reject
            $myRejected = ApprovalQueue::where('approver_id', $user->employee_id)
                            ->where('status', 'REJECTED')
                            ->count();

            // TABEL: Tampilkan Surat yang ada di antrian saya (Urutkan yg Pending duluan)
            $recent_rls = RequisitionLetter::whereHas('approvalQueues', function($q) use ($user) {
                                $q->where('approver_id', $user->employee_id);
                            })
                            ->with(['approvalQueues' => function($q) use ($user) {
                                $q->where('approver_id', $user->employee_id);
                            }])
                            ->get()
                            ->sortBy(function($rl) use ($user) {
                                // Custom Sort: PENDING paling atas
                                $myQueue = $rl->approvalQueues->first();
                                return $myQueue->status === 'PENDING' ? 0 : 1;
                            });

        } else {
            // === LOGIC UNTUK STAFF (REQUESTER) ===
            // (Ini logic lama yang sudah benar untuk Budi)

            $myTotal = RequisitionLetter::where('requester_id', $user->employee_id)->count();

            $myPending = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->whereIn('status_flow', ['ON_PROGRESS', 'DRAFT'])->count();

            $myApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->where('status_flow', 'APPROVED')->count();

            $myRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->where('status_flow', 'REJECTED')->count();

            $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
        }

        return view('dashboard', compact(
            'myTotal', 'myPending', 'myApproved', 'myRejected', 'recent_rls'
        ));
    }
}
