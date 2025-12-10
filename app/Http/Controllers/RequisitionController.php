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
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.uom' => 'required|string',
        ]);

        // Gunakan DB Transaction agar kalau item gagal, header tidak tersimpan
        DB::transaction(function () use ($request) {
            $user = Auth::user();

$status = ($request->action === 'draft') ? 'DRAFT' : 'ON_PROGRESS';

            // A. SIMPAN HEADER SURAT
            $rl = RequisitionLetter::create([
                'company_id' => $user->company_id,
                'requester_id' => $user->employee_id,
                'rl_no' => RequisitionLetter::generateNumber(),
                'request_date' => $request->request_date,

                // PERBAIKAN DISINI: Gunakan variabel $status, jangan 'ON_PROGRESS'
                'status_flow' => $status,

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

            // 4. GENERATE APPROVAL (HANYA JIKA SUBMIT / ON_PROGRESS)
            // Kalau Draft, jangan panggil manager dulu.
            if ($status === 'ON_PROGRESS') {
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
            }
        });

        $msg = ($request->action === 'draft') ? 'Draft berhasil disimpan!' : 'Permintaan berhasil diajukan!';
        return redirect()->route('dashboard')->with('success', $msg);
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
// Jangan lupa import di paling atas:
    // use Barryvdh\DomPDF\Facade\Pdf;

    public function printPdf($id)
    {
        // Ambil data lengkap dengan relasi ke Approval
        $rl = RequisitionLetter::with([
            'items', 
            'requester.department', 
            'company',
            'approvalQueues.approver.position' // Ambil data approver & jabatannya
        ])->findOrFail($id);

        // Load View khusus PDF
        $pdf = Pdf::loadView('requisitions.pdf', compact('rl'));
        
        // Setup Kertas A4 Potrait
        $pdf->setPaper('a4', 'portrait');

        // Render (Stream = Buka di browser, Download = Langsung unduh file)
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

    // 6. SUBMIT DARI DRAFT (Action dari Halaman Detail)
    public function submitDraft($id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        // Security Check: Pastikan statusnya masih DRAFT
        if ($rl->status_flow != 'DRAFT') {
            return back()->with('error', 'Dokumen ini sudah diajukan atau diproses.');
        }

        // Security Check: Pastikan yang submit adalah pembuatnya sendiri
        if (Auth::user()->employee_id != $rl->requester_id) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        DB::transaction(function () use ($rl) {
            // 1. Update Status Jadi ON_PROGRESS
            $rl->update(['status_flow' => 'ON_PROGRESS']);

            // 2. GENERATE APPROVAL QUEUE (Copy Logic dari Store)
            // Karena saat Draft approval belum dibuat, sekarang saatnya dibuat.

            $manager = User::where('company_id', $rl->company_id)
                        ->whereHas('position', function($q) {
                            $q->where('position_name', 'Manager');
                        })->first();

            if ($manager) {
                // Cek dulu biar gak duplikat (safety)
                $existingQueue = ApprovalQueue::where('rl_id', $rl->id)->exists();
                if (!$existingQueue) {
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $manager->employee_id,
                        'level_order' => 1,
                        'status' => 'PENDING'
                    ]);
                }
            }
        });

        return redirect()->route('dashboard')->with('success', 'Draft berhasil diajukan untuk approval!');
    }
    // Method Khusus untuk Preview (Tanpa Simpan Database)
    public function previewTemp(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'request_date' => 'required|date',
            'subject' => 'required|string',
            'items' => 'required|array',
            'items.*.item_name' => 'required',
            'items.*.qty' => 'required',
        ]);

        // 2. Buat Dummy Object Requisition Letter (In Memory)
        $rl = new RequisitionLetter();
        $rl->rl_no = 'DRAFT-PREVIEW-001'; // Nomor Sementara
        $rl->request_date = $request->request_date;
        $rl->subject = $request->subject;
        $rl->to_department = 'Purchasing / Procurement'; // Default
        $rl->priority = $request->priority;
        $rl->required_date = $request->required_date;
        $rl->status_flow = 'DRAFT';
        
        // 3. Set Relasi Dummy (PENTING!)
        
        // A. Relasi Requester (Ambil User Login + Load Dept & Position)
        $user = Auth::user()->load(['department', 'position']); 
        $rl->setRelation('requester', $user);
        
        // B. Relasi Company (Ambil dari User)
        $rl->setRelation('company', $user->company);
        
        // C. Relasi Items (Looping data dari Form)
        $items = collect(); // Koleksi kosong
        if($request->has('items')){
            foreach ($request->items as $itemData) {
                // Buat object item sementara
                $item = new \App\Models\RequisitionItem($itemData);
                $items->push($item);
            }
        }
        $rl->setRelation('items', $items);

        // D. Relasi Approval (INI YANG BIKIN ERROR SEBELUMNYA)
        // Kita harus kirim Collection kosong agar PDF tidak error saat panggil $rl->approvalQueues->where(...)
        $rl->setRelation('approvalQueues', collect([])); 

        // 4. Generate PDF
        $pdf = Pdf::loadView('requisitions.pdf', compact('rl'));
        $pdf->setPaper('a4', 'portrait');

        // 5. Return Stream
        return $pdf->stream('preview.pdf');
    }
}
