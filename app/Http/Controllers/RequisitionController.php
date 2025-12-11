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
// 2. SIMPAN DATA (DATABASE TRANSACTION)
    public function store(Request $request)
    {
        // Validasi Input (Updated)
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1', // numeric agar aman
            'items.*.uom' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            $status = ($request->action === 'draft') ? 'DRAFT' : 'ON_PROGRESS';

            // A. GENERATE NOMOR REAL (Re-Generate saat save agar urut)
            // Logic penomoran dipanggil lagi disini atau pakai helper model
            $rlNo = RequisitionLetter::generateNumber();
            // Note: Jika pakai logic singkatan (IT) seperti di preview, copas logicnya kesini.

            // B. SIMPAN HEADER SURAT (UPDATE FIELD BARU)
            $rl = RequisitionLetter::create([
                'company_id' => $user->company_id,
                'requester_id' => $user->employee_id,
                'rl_no' => $rlNo,
                'request_date' => $request->request_date,
                'status_flow' => $status,
                'subject' => $request->subject,

                // FIELD BARU (PENTING)
                'to_department' => 'Purchasing / Procurement', // Default
                'priority' => $request->priority,           // <--- BARU
                'required_date' => $request->required_date, // <--- BARU
                'remark' => $request->remark,
            ]);

            // C. SIMPAN ITEMS (UPDATE FIELD BARU)
            foreach ($request->items as $item) {
                RequisitionItem::create([
                    'rl_id' => $rl->id,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'uom' => $item['uom'],
                    'description' => $item['description'] ?? null,

                    // FIELD BARU (PENTING)
                    'part_number' => $item['part_number'] ?? null,     // <--- BARU
                    'stock_on_hand' => $item['stock_on_hand'] ?? 0,    // <--- BARU

                    'status_item' => 'WAITING'
                ]);
            }

            // D. GENERATE APPROVAL (HANYA JIKA SUBMIT)
            if ($status === 'ON_PROGRESS') {
                $manager = User::where('company_id', $user->company_id)
                            ->where('department_id', $user->department_id) // Manager dept sendiri
                            ->whereHas('position', function($q) {
                                $q->where('position_name', 'Manager');
                            })->first();

                // Fallback: Jika tidak ada manager di dept itu, cari sembarang manager (opsional)
                if (!$manager) {
                     $manager = User::where('company_id', $user->company_id)
                                ->whereHas('position', function($q) {
                                    $q->where('position_name', 'Manager');
                                })->first();
                }

                if ($manager) {
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $manager->employee_id,
                        'level_order' => 1,
                        'status' => 'PENDING'
                    ]);
                }

                // Opsional: Langsung generate level 2 (Director) status PENDING juga?
                // Biasanya Level 2 dibuat setelah Level 1 Approved. (Biarkan logic di controller approval).
            }
        });

        $msg = ($request->action === 'draft') ? 'Draft berhasil disimpan!' : 'Permintaan berhasil diajukan!';
        return redirect()->route('dashboard')->with('success', $msg);
    }

    // 3. TAMPILKAN DETAIL SURAT (Untuk Review)
// 3. TAMPILKAN DETAIL SURAT (REVISI)
    public function show($id)
    {
        $rl = RequisitionLetter::with([
            'items', // Load items standar
            'requester.department',
            'approvalQueues.approver.position', // Load data approver lengkap
            'company'
        ])->findOrFail($id);

        return view('requisitions.show', compact('rl'));
    }

    // 4. PRINT PDF (REVISI FIX ERROR SLASH)
    public function printPdf($id)
    {
        $rl = RequisitionLetter::with([
            'items',
            'requester.department',
            'company',
            'approvalQueues.approver.position'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl'));
        $pdf->setPaper('a4', 'portrait');

        // FIX ERROR DISINI:
        // Ganti tanda "/" dengan "-" agar tidak dianggap folder oleh browser
        $safeFilename = 'RL-' . str_replace(['/', '\\'], '-', $rl->rl_no) . '.pdf';

        return $pdf->stream($safeFilename);
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
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'required|string',
            'items' => 'required|array',
        ]);

        $user = Auth::user()->load(['department', 'position']);
        $company = \App\Models\Company::find($user->company_id);

        // 2. GENERATE NOMOR REAL (PREDIKSI AKURAT)
        $companyCode = $company->company_code ?? 'GEN';

        // Ambil Singkatan Dept (IT, HR, FIN)
        $deptFull = $user->department->department_name ?? 'GEN';
        $deptParts = preg_split('/[\s(]/', $deptFull);
        $deptCode = strtoupper($deptParts[0]);

        $month = date('m', strtotime($request->request_date)); // 12
        $year = date('Y', strtotime($request->request_date));  // 2025

        // Hitung urutan nomor selanjutnya di DB
        $count = RequisitionLetter::where('company_id', $user->company_id)
                    ->whereYear('request_date', $year)
                    ->whereMonth('request_date', $month)
                    ->count();

        // Jika nomor ini belum disave, berarti dia nomor ke (count + 1)
        $nextNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        // FORMAT FINAL: RL/ASM/IT/2025/12/0001
        $realDraftNo = "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$nextNo}";

        // 3. OBJECT RL DUMMY
        $rl = new RequisitionLetter();
        $rl->rl_no = $realDraftNo; // <-- SEKARANG SUDAH NOMOR ASLI
        $rl->request_date = $request->request_date;
        $rl->subject = $request->subject;
        $rl->to_department = 'Purchasing / Procurement';
        $rl->priority = $request->priority;
        $rl->required_date = $request->required_date;
        $rl->remark = $request->remark; // <-- MASUKKAN REMARK DISINI
        $rl->status_flow = 'DRAFT';

        // RELASI
        $rl->setRelation('requester', $user);
        $rl->setRelation('company', $company);
        $rl->setRelation('approvalQueues', collect([]));

        // ITEMS
        $items = collect();
        if($request->has('items')){
            foreach ($request->items as $itemData) {
                $item = new \App\Models\RequisitionItem($itemData);
                $items->push($item);
            }
        }
        $rl->setRelation('items', $items);

        // APPROVER INFO
        $manager = User::where('company_id', $user->company_id)
                    ->where('department_id', $user->department_id)
                    ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })
                    ->first();

        $director = User::where('company_id', $user->company_id)
                    ->whereHas('position', function($q){ $q->where('position_name', 'Director'); })
                    ->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }
}
