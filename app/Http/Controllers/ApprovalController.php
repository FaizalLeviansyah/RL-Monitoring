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

        // --- FIX LOGIKA PENGIRIMAN WA ---

        // 1. Ambil No HP dari Database Master (tbl_employee)
        // Karena di tabel 'users' lokal tidak ada kolom phone
        $employeeMaster = DB::connection('mysql_master')
                            ->table('tbl_employee')
                            ->where('employee_id', $rl->requester_id)
                            ->first();

        // Pastikan Anda cek nama kolom di tbl_employee, apakah 'phone', 'mobile_phone', atau 'no_hp'?
        // Di sini saya asumsikan namanya 'phone'
        $requesterPhone = $employeeMaster ? $employeeMaster->phone : null;

        if ($requesterPhone) {
            $link = route('requisitions.show', $rl->id);
            $pesan = "Halo *{$rl->requester->full_name}*,\n\nDokumen RL No: *{$rl->rl_no}* telah divalidasi Manager.\nStatus: *PARTIALLY APPROVED*\n\nSilakan minta TTD Direktur, Scan, lalu Upload dokumen final di sistem.\nLink: {$link}";

            // Hapus Try-Catch sementara agar kalau error kelihatan di layar
            // try {
                WaService::send($requesterPhone, $pesan);
            // } catch (\Exception $e) {
            //    \Illuminate\Support\Facades\Log::error("WA Error: " . $e->getMessage());
            // }
        } else {
            // Debugging: Jika No HP tidak ketemu
            return back()->with('warning', 'Dokumen Approved, tapi WA gagal kirim karena No HP Requester tidak ditemukan di Database Master.');
        }

        return back()->with('success', 'Dokumen tervalidasi. Notifikasi WA dikirim ke Requester.');
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
