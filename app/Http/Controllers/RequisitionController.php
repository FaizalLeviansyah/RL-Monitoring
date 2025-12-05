<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\RequisitionItem;
use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan use ini di paling atas
class RequisitionController extends Controller
{
    // 1. TAMPILKAN FORM
    public function create()
    {
        // Generate Nomor Surat (Preview)
        $newNumber = RequisitionLetter::generateNumber();
        return view('requisitions.create', compact('newNumber'));
    }

    // 2. SIMPAN DATA (DATABASE TRANSACTION)
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'items' => 'required|array|min:1', // Wajib minimal 1 barang
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.uom' => 'required|string',
        ]);

        // Gunakan DB Transaction agar kalau item gagal, header tidak tersimpan
        DB::transaction(function () use ($request) {
            $user = Auth::user();

            // A. SIMPAN HEADER SURAT
            $rl = RequisitionLetter::create([
                'company_id' => $user->company_id,
                'requester_id' => $user->employee_id,
                'rl_no' => RequisitionLetter::generateNumber(), // Generate lagi saat save biar aman
                'request_date' => $request->request_date,
                'status_flow' => 'ON_PROGRESS', // Langsung jalan
                'subject' => $request->subject,
                'to_department' => $request->to_department,
                'remark' => $request->remark,
            ]);

            // B. SIMPAN ITEMS (Looping)
            foreach ($request->items as $item) {
                RequisitionItem::create([
                    'rl_id' => $rl->id,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'uom' => $item['uom'],
                    'description' => $item['description'] ?? null,
                    'status_item' => 'WAITING'
                ]);
            }

            // C. GENERATE APPROVAL QUEUE (AUTO)
            // Skenario: Cari Manager di perusahaan yg sama
            // (Nanti logic ini bisa dipercanggih, sementara kita hardcode logic dulu)

            // Level 1: Manager (Cari User yg jabatannya Manager di Company user)
            // Note: Ini contoh simplifikasi. Nanti kita bisa pakai position_id yg Bapak buat.
            $manager = User::where('company_id', $user->company_id)
                        ->whereHas('position', function($q) {
                            $q->where('position_name', 'Manager');
                        })->first();

            if ($manager) {
                ApprovalQueue::create([
                    'rl_id' => $rl->id,
                    'approver_id' => $manager->employee_id,
                    'level_order' => 1,
                    'status' => 'PENDING'
                ]);
            }
            // Level 2: Director ... (Bisa ditambahkan nanti)
        });
        return redirect()->route('dashboard')->with('success', 'Requisition Letter created successfully!');
    }

    // 3. TAMPILKAN DETAIL SURAT (Untuk Review)
    public function show($id)
    {
        // Ambil data surat beserta relasinya (Items, User, Approvals)
        // Hapus 'department' dari array with()
        $rl = RequisitionLetter::with(['items', 'requester.department', 'approvalQueues.approver', 'company'])
                ->findOrFail($id);

        return view('requisitions.show', compact('rl'));
    }

    // 4. PRINT PDF
// 4. CETAK PDF
    public function printPdf($id)
    {
        $rl = RequisitionLetter::with(['items', 'requester.department', 'company'])
                ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('requisitions.pdf', compact('rl'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('RL-'.$rl->rl_no.'.pdf');
    }
}
