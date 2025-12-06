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
        // UPDATE: Tambahkan 'items.supplyHistories.receiver' di dalam with()
        // Agar kita bisa ambil data history dan nama penerimanya
        $rl = RequisitionLetter::with([
            'items.supplyHistories.receiver', 
            'requester.department', 
            'approvalQueues.approver', 
            'company'
        ])->findOrFail($id);
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

    // ... (kode method sebelumnya: printPdf dll)

    // 5. HALAMAN LIST BERDASARKAN STATUS
    public function listByStatus($status)
    {
        // Validasi status agar user tidak ketik sembarangan di URL
        $validStatuses = ['DRAFT', 'ON_PROGRESS', 'APPROVED', 'REJECTED'];
        $statusUpper = strtoupper($status);

        if (!in_array($statusUpper, $validStatuses)) {
            abort(404); // Halaman tidak ditemukan jika status ngawur
        }

        $user = Auth::user();
        $query = RequisitionLetter::with(['requester.department', 'company'])
                    ->where('status_flow', $statusUpper)
                    ->orderBy('created_at', 'desc');

        // LOGIC ROLE:
        // Staff hanya lihat punya sendiri.
        // Manager/Direktur bisa lihat semua (atau filter per departemen jika perlu).

        // Cek apakah dia Approver (Manager/Director)
        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        // Jika Staff Biasa, filter punya sendiri
        if (!$isApprover) {
             $query->where('requester_id', $user->employee_id);
        }

        // Gunakan Pagination (10 baris per halaman) biar tidak berat
        $requisitions = $query->paginate(10);

        return view('requisitions.index', compact('requisitions', 'status', 'statusUpper'));
    }
}
