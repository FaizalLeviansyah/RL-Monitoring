<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\RequisitionLetter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    // 1. ACTION APPROVE (Menyetujui)
    public function approve($id)
    {
        $queue = ApprovalQueue::findOrFail($id);

        // Security: Pastikan yang approve adalah user yang login
        if ($queue->approver_id != Auth::user()->employee_id) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($queue) {
            // A. Update Status Queue Jadi APPROVED
            $queue->update([
                'status' => 'APPROVED',
                'approved_at' => now(),
            ]);

            // B. Cek Apakah Masih Ada Level Selanjutnya?
            // Logikanya: Jika ini Level 1 (Manager), maka lanjut ke Level 2 (Director)

            if ($queue->level_order == 1) {
                // --- LANJUT KE LEVEL 2 (DIRECTOR) ---

                // Cari Direktur (Bisa spesifik per PT atau Global)
                // Asumsi: Direktur ada di PT yang sama
                $rl = $queue->requisitionLetter; // Pastikan Model ApprovalQueue punya relasi ini

                $director = User::where('company_id', $rl->company_id)
                                ->whereHas('position', function($q){
                                    $q->where('position_name', 'Director');
                                })->first();

                if ($director) {
                    // Buat Antrian Baru untuk Direktur
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $director->employee_id,
                        'level_order' => 2,
                        'status' => 'PENDING' // Direktur statusnya Pending
                    ]);
                } else {
                    // Jika tidak ada direktur, anggap selesai (Auto Finish) - Optional
                    $rl->update(['status_flow' => 'APPROVED']);
                }

            } elseif ($queue->level_order == 2) {
                // --- FINISH (LEVEL TERAKHIR) ---

                // Update Status Utama Surat Jadi APPROVED
                $queue->requisitionLetter->update(['status_flow' => 'APPROVED']);
            }
        });

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }

    // 2. ACTION REJECT (Menolak)
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $queue = ApprovalQueue::findOrFail($id);

        if ($queue->approver_id != Auth::user()->employee_id) {
            abort(403);
        }

        DB::transaction(function () use ($queue, $request) {
            // Update Queue jadi REJECTED
            $queue->update([
                'status' => 'REJECTED',
                'reason_rejection' => $request->reason,
                'updated_at' => now(),
            ]);

            // Update Surat Utama jadi REJECTED (Mati)
            $queue->requisitionLetter->update(['status_flow' => 'REJECTED']);
        });

        return redirect()->back()->with('error', 'Dokumen ditolak. Requester diminta revisi.');
    }
}
