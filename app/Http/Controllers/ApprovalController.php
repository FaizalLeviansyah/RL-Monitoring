<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\RequisitionLetter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WaService; // <--- PENTING: Panggil Service WA

class ApprovalController extends Controller
{
    // ACTION: APPROVE (+ NOTIF WA DIRECTOR)
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        $queue = ApprovalQueue::where('rl_id', $id)
                    ->where('approver_id', $user->employee_id)
                    ->where('status', 'PENDING')
                    ->first();

        if (!$queue) {
            return back()->with('error', 'Anda tidak memiliki antrian approval untuk dokumen ini.');
        }

        $rl = RequisitionLetter::findOrFail($id);
        $notifDirector = null;

        DB::transaction(function () use ($queue, $rl, $user, &$notifDirector, $request) {
            // 1. Update Status Approver Sekarang (Manager)
            $queue->update([
                'status' => 'APPROVED',
                'approved_at' => now(),
                'note' => $request->note
            ]);

            if ($queue->level_order == 1) {
                $director = User::where('company_id', $rl->company_id)
                            ->whereHas('position', function($q) {
                                $q->where('position_name', 'Director');
                            })->first();

                if ($director) {

                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $director->employee_id,
                        'level_order' => 2,
                        'status' => 'PENDING'
                    ]);

                    $notifDirector = [
                        'target' => $director,
                        'rl' => $rl,
                        'manager_name' => $user->full_name
                    ];
                } else {
                    $rl->update(['status_flow' => 'APPROVED']);
                }

            }
            elseif ($queue->level_order == 2) {
                $rl->update(['status_flow' => 'APPROVED']);
            }
        });

        if ($notifDirector && !empty($notifDirector['target']->phone_number)) {
            $target = $notifDirector['target'];
            $link = route('requisitions.show', $rl->id);

            $pesan = "Halo Bpk/Ibu *{$target->full_name}*,\n\n";
            $pesan .= "Dokumen RL berikut telah disetujui oleh Manager *{$notifDirector['manager_name']}* dan sekarang menunggu persetujuan Anda.\n\n";
            $pesan .= "📝 No RL: *{$rl->rl_no}*\n";
            $pesan .= "👤 Requester: {$rl->requester->full_name}\n";
            $pesan .= "📄 Subject: {$rl->subject}\n\n";
            $pesan .= "Klik link berikut untuk Approve:\n{$link}\n\n";
            $pesan .= "_RL Monitoring System_";

            WaService::send($target->phone_number, $pesan);
        }

        return back()->with('success', 'Dokumen berhasil disetujui.');
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

        $rl = RequisitionLetter::findOrFail($id);

        DB::transaction(function () use ($queue, $rl, $request) {
            $queue->update([
                'status' => 'REJECTED',
                'approved_at' => now(),
                'note' => $request->reason
            ]);

            $rl->update(['status_flow' => 'REJECTED']);
        });

        return back()->with('success', 'Dokumen ditolak.');
    }
}
