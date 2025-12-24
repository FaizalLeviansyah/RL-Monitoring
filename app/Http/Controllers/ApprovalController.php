<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\RequisitionLetter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WaService;

class ApprovalController extends Controller
{
    // --- FUNGSI ACTION UTAMA ---
    public function action(Request $request)
    {
        $action = $request->input('action');
        $id = $request->input('rl_id') ?? $request->input('id');

        if ($action === 'approve') {
            return $this->approve($request, $id);
        } elseif ($action === 'reject') {
            return $this->reject($request, $id);
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }

    // --- LOGIC APPROVE (LEVEL 1 & LEVEL 2) ---
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        // 1. Validasi: Apakah User ini punya antrian PENDING untuk dokumen ini?
        $queue = ApprovalQueue::where('rl_id', $id)
                    ->where('approver_id', $user->employee_id)
                    ->where('status', 'PENDING')
                    ->first();

        if (!$queue) {
            return back()->with('error', 'Akses ditolak atau dokumen sudah diproses sebelumnya.');
        }

        $rl = RequisitionLetter::with('requester')->findOrFail($id);
        $nextApprover = null;

        DB::transaction(function () use ($queue, $rl, $request, &$nextApprover) {
            // A. Update Status Queue User Saat Ini -> APPROVED
            $queue->update([
                'status' => 'APPROVED',
                'approved_at' => now(),
                'note' => $request->note // Opsional jika ada catatan
            ]);

            // B. Cek Level Approval Saat Ini
            if ($queue->level_order == 1) {
                // === JIKA MANAGER (Level 1) APPROVE ===

                // 1. Update Status Dokumen
                $rl->update(['status_flow' => 'PARTIALLY_APPROVED']);

                // 2. Cari Siapa Direkturnya (Logic Managing Director masuk disini)
                $directorTitles = [
                    'Managing Director',
                    'Deputy Managing Director',
                    'Director',
                    'President Director',
                    'General Manager'
                ];

                // Cari user dengan jabatan direktur di perusahaan yang sama
                $director = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) use ($directorTitles) {
                        $q->whereIn('position_name', $directorTitles);
                    })->first();

                if ($director) {
                    // 3. Buat Antrian Baru untuk Direktur (Level 2)
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $director->employee_id,
                        'level_order' => 2,
                        'status' => 'PENDING'
                    ]);
                    $nextApprover = $director;
                }

            } elseif ($queue->level_order == 2) {
                // === JIKA DIREKTUR (Level 2) APPROVE ===

                // Final Approval -> Status jadi WAITING_SUPPLY (Menunggu Barang)
                $rl->update(['status_flow' => 'WAITING_SUPPLY']);
            }
        });

        // --- C. KIRIM NOTIFIKASI WHATSAPP (SAFE DB CONNECTION) ---
        $this->sendWhatsAppNotification($rl, $queue, $user, $nextApprover);

        return back()->with('success', 'Dokumen berhasil disetujui (Approved).');
    }

    // --- LOGIC REJECT ---
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
            // Update Queue jadi REJECTED
            $queue->update([
                'status' => 'REJECTED',
                'approved_at' => now(), // Tanggal reject
                'note' => $request->reason
            ]);

            // Update Status Dokumen Utama jadi REJECTED (Workflow Berhenti)
            $rl->update(['status_flow' => 'REJECTED']);
        });

        // Kirim Notif Reject ke Requester
        $this->sendRejectNotification($rl, $user, $request->reason);

        return back()->with('success', 'Dokumen ditolak (Rejected).');
    }

    // --- PRIVATE HELPER: WA NOTIFICATION (FIXED DB) ---
    private function sendWhatsAppNotification($rl, $queue, $approver, $nextApprover)
    {
        // 1. Setup Koneksi ke Database Master (tempat tbl_employee berada)
        $userModel = new User();
        $userDb = $userModel->getConnection()->getDatabaseName(); // Nama DB (misal: db_master)

        // Ambil Data Requester (termasuk No HP)
        $requesterData = DB::connection($userModel->getConnectionName())
                            ->table($userDb . '.tbl_employee')
                            ->where('employee_id', $rl->requester_id)
                            ->first();

        $requesterPhone = $requesterData ? $requesterData->phone : null;

        if ($requesterPhone) {
            $link = route('requisitions.show', $rl->id);

            if ($queue->level_order == 1) {
                // Pesan jika Manager Approve
                $pesan = "Halo *{$rl->requester->full_name}*,\n\n";
                $pesan .= "RL No: *{$rl->rl_no}* telah disetujui oleh Manager (*{$approver->full_name}*).\n";
                $pesan .= "Status: *WAITING DIRECTOR*\n\n";
                $pesan .= "Mohon pantau terus status pengajuan Anda.\nLink: {$link}";
            } else {
                // Pesan jika Direktur Approve (Final)
                $pesan = "Selamat *{$rl->requester->full_name}*! \n\n";
                $pesan .= "RL No: *{$rl->rl_no}* telah disetujui sepenuhnya oleh (*{$approver->full_name}*).\n";
                $pesan .= "Status: *FINAL APPROVED (Waiting Supply)*\n\n";
                $pesan .= "Proses pengadaan barang akan segera dilakukan.\nLink: {$link}";
            }

            try {
                WaService::send($requesterPhone, $pesan);
            } catch (\Exception $e) {
                // Silent fail agar tidak error 500
            }
        }

        // 2. Jika ada Next Approver (Direktur), kirim WA ke Direktur tsb
        if ($nextApprover) {
            $dirData = DB::connection($userModel->getConnectionName())
                         ->table($userDb . '.tbl_employee')
                         ->where('employee_id', $nextApprover->employee_id)
                         ->first();

            $dirPhone = $dirData ? $dirData->phone : null;

            if ($dirPhone) {
                $linkDir = route('requisitions.show', $rl->id);
                $pesanDir = "Halo Bpk/Ibu *{$nextApprover->full_name}*,\n\n";
                $pesanDir .= "Ada pengajuan RL (Level Direktur) menunggu persetujuan Anda.\n";
                $pesanDir .= "No: *{$rl->rl_no}*\n";
                $pesanDir .= "Requester: {$rl->requester->full_name}\n\n";
                $pesanDir .= "Silakan review dan approve disini:\n{$linkDir}";

                try {
                    WaService::send($dirPhone, $pesanDir);
                } catch (\Exception $e) {}
            }
        }
    }

    private function sendRejectNotification($rl, $rejector, $reason)
    {
        // Setup Koneksi
        $userModel = new User();
        $userDb = $userModel->getConnection()->getDatabaseName();

        $requesterData = DB::connection($userModel->getConnectionName())
                            ->table($userDb . '.tbl_employee')
                            ->where('employee_id', $rl->requester_id)
                            ->first();

        $phone = $requesterData ? $requesterData->phone : null;

        if ($phone) {
            $pesan = "Halo *{$rl->requester->full_name}*,\n\n";
            $pesan .= "Mohon maaf, RL No: *{$rl->rl_no}* telah *DITOLAK* oleh {$rejector->full_name}.\n";
            $pesan .= "Alasan: {$reason}\n\n";
            $pesan .= "Silakan perbaiki atau buat pengajuan baru.";

            try {
                WaService::send($phone, $pesan);
            } catch (\Exception $e) {}
        }
    }
}
