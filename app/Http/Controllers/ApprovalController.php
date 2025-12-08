<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\OtpAuditLog; // Kita tetap pakai tabel ini untuk log history klik
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function action(Request $request)
    {
        $request->validate([
            'queue_id' => 'required|exists:approval_queues,id',
            'action' => 'required|in:APPROVE,REJECT'
        ]);

        $queue = ApprovalQueue::with('letter')->findOrFail($request->queue_id);
        $user = Auth::user();

        // Security Check: Pastikan yang klik adalah Approver asli
        if ($queue->approver_id != $user->employee_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk approval ini.');
        }
        
        // Tentukan Status Baru
        $newStatus = ($request->action == 'APPROVE') ? 'APPROVED' : 'REJECTED';

        // 1. Update Status Queue
        $queue->update([
            'status' => $newStatus,
            'approved_at' => now(),
            'method' => 'MANUAL_CLICK' // Penanda bahwa ini approval via web
        ]);

        // 2. Update Status Surat Utama (Header)
        if ($newStatus == 'APPROVED') {
            // Logic Sederhana: Kalau Manager ACC -> Surat dianggap ACC.
            // (Nanti bisa ditambah logic cek direktur disini)
            $queue->letter->update(['status_flow' => 'APPROVED']);
        } else {
            $queue->letter->update(['status_flow' => 'REJECTED']);
        }

        // 3. Catat Log (Audit Trail)
        OtpAuditLog::create([
            'rl_id' => $queue->rl_id,
            'user_id' => $user->employee_id,
            'action' => 'MANUAL_' . $newStatus,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', 'Dokumen berhasil diproses: ' . $newStatus);
    }
}
