<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\RequisitionLetter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WaService;

class ApprovalController extends Controller
{
    // --- FUNGSI ACTION (Wajib Ada karena dipanggil route '/approval/action') ---
    public function action(Request $request)
    {
        // Ambil value dari tombol yang diklik (name="action")
        $action = $request->input('action');
        $id = $request->input('rl_id') ?? $request->input('id');

        if ($action === 'approve') {
            return $this->approve($request, $id);
        } elseif ($action === 'reject') {
            return $this->reject($request, $id);
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }

    // ACTION: APPROVE
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        $queue = ApprovalQueue::where('rl_id', $id)
                    ->where('approver_id', $user->employee_id)
                    ->where('status', 'PENDING')
                    ->first();

        if (!$queue) {
            return back()->with('error', 'Akses ditolak atau dokumen sudah diproses.');
        }

        $rl = RequisitionLetter::with('requester')->findOrFail($id);

        DB::transaction(function () use ($queue, $rl, $request) {
            $queue->update([
                'status' => 'APPROVED',
                'approved_at' => now(),
                'note' => $request->note
            ]);

            // Update jadi PARTIALLY_APPROVED
            $rl->update(['status_flow' => 'PARTIALLY_APPROVED']);
        });

        // NOTIF WA BALIK KE REQUESTER
        if ($rl->requester && !empty($rl->requester->phone)) {
            $link = route('requisitions.show', $rl->id);
            $pesan = "Halo *{$rl->requester->full_name}*,\n\nDokumen RL No: *{$rl->rl_no}* telah divalidasi Manager.\nStatus: *PARTIALLY APPROVED*\n\nSilakan minta TTD Direktur, Scan, lalu Upload dokumen final di sistem.\nLink: {$link}";

            try {
                WaService::send($rl->requester->phone, $pesan);
            } catch (\Exception $e) {
                // Lanjut meski WA gagal
            }
        }

        return back()->with('success', 'Dokumen tervalidasi. Requester telah dinotifikasi.');
    }

    // ACTION: REJECT
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        $user = Auth::user();
        $queue = ApprovalQueue::where('rl_id', $id)
                    ->where('approver_id', $user->employee_id)
                    ->where('status', 'PENDING')
                    ->first();

        if (!$queue) return back()->with('error', 'Akses ditolak.');

        $rl = RequisitionLetter::with('requester')->findOrFail($id);

        DB::transaction(function () use ($queue, $rl, $request) {
            $queue->update([
                'status' => 'REJECTED',
                'approved_at' => now(),
                'note' => $request->reason
            ]);
            $rl->update(['status_flow' => 'REJECTED']);
        });

        // NOTIF WA REJECT
        if ($rl->requester && !empty($rl->requester->phone)) {
            $pesan = "Halo *{$rl->requester->full_name}*, RL No: *{$rl->rl_no}* DITOLAK Manager.\nAlasan: {$request->reason}";
            try {
                WaService::send($rl->requester->phone, $pesan);
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'Dokumen ditolak.');
    }
}
