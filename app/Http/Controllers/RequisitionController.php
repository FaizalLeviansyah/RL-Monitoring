<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\RequisitionItem;
use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WaService;

class RequisitionController extends Controller
{
    // 1. CREATE FORM
    public function create()
    {
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();
        $newRlNumber = \App\Models\RequisitionLetter::generateNumber();
        $user = Auth::user();

        return view('requisitions.create', [
            'newNumber' => $newRlNumber,
            'user' => $user,
            'masterItems' => $masterItems
        ]);
    }

    // 2. STORE DATA
    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uom' => 'required|string',
        ]);

        $notifData = null;

        DB::transaction(function () use ($request, &$notifData) {
            $user = Auth::user();
            $status = ($request->action === 'draft') ? 'DRAFT' : 'ON_PROGRESS';
            $rlNo = RequisitionLetter::generateNumber();

            $rl = RequisitionLetter::create([
                'company_id' => $user->company_id,
                'requester_id' => $user->employee_id,
                'rl_no' => $rlNo,
                'request_date' => $request->request_date,
                'status_flow' => $status,
                'subject' => $request->subject,
                'to_department' => 'Purchasing / Procurement',
                'priority' => $request->priority,
                'required_date' => $request->required_date,
                'remark' => $request->remark,
            ]);

            foreach ($request->items as $item) {
                RequisitionItem::create([
                    'rl_id' => $rl->id,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'uom' => $item['uom'],
                    'description' => $item['description'] ?? null,
                    'part_number' => $item['part_number'] ?? null,
                    'stock_on_hand' => $item['stock_on_hand'] ?? 0,
                    'status_item' => 'WAITING'
                ]);
            }

            if ($status === 'ON_PROGRESS') {
                $manager = User::where('company_id', $user->company_id)
                    ->where('department_id', $user->department_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();

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

                    $notifData = [
                        'target' => $manager,
                        'rl' => $rl,
                        'sender' => $user->full_name
                    ];
                }
            }
        });

        $mainMsg = ($request->action === 'draft') ? 'Draft berhasil disimpan' : 'Permintaan berhasil diajukan';
        $waStatusMsg = "";

        if ($notifData) {
            if (!empty($notifData['target']->phone)) {
                $target = $notifData['target'];
                $rl = $notifData['rl'];
                $link = route('requisitions.show', $rl->id);

                $pesan = "Halo Bpk/Ibu *{$target->full_name}*,\n\n";
                $pesan .= "Ada pengajuan *Requisition Letter* baru menunggu persetujuan Anda.\n\n";
                $pesan .= "📝 No RL: *{$rl->rl_no}*\n";
                $pesan .= "👤 Requester: {$notifData['sender']}\n";
                $pesan .= "📄 Subject: {$rl->subject}\n\n";
                $pesan .= "Klik link berikut untuk Approval:\n{$link}\n\n";
                $pesan .= "_RL Monitoring System_";

                $result = WaService::send($target->phone, $pesan);

                if ($result['status']) {
                    $waStatusMsg = " & " . $result['msg'];
                } else {
                    $waStatusMsg = " (Namun WA Gagal: " . $result['msg'] . ")";
                }
            } else {
                $waStatusMsg = " (Info: Notifikasi WA tidak dikirim karena No. HP Manager belum diisi)";
            }
        }

        return redirect()->route('dashboard')->with('success', $mainMsg . $waStatusMsg . '!');
    }

    // 3. SHOW DETAIL
    public function show($id)
    {
        $rl = RequisitionLetter::with([
            'items',
            'requester.department',
            'approvalQueues.approver.position',
            'company'
        ])->findOrFail($id);

        return view('requisitions.show', compact('rl'));
    }

    // 4. PRINT PDF
    public function printPdf($id)
    {
        $rl = RequisitionLetter::with([
            'items',
            'requester.department',
            'company',
            'approvalQueues.approver.position'
        ])->findOrFail($id);

        $manager = User::where('company_id', $rl->company_id)
            ->where('department_id', $rl->requester->department_id)
            ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();

        if (!$manager) {
            $manager = User::where('company_id', $rl->company_id)
                ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();
        }

        $director = User::where('company_id', $rl->company_id)
            ->whereHas('position', function($q) { $q->where('position_name', 'Director'); })->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');
        $safeFilename = 'RL-' . str_replace(['/', '\\'], '-', $rl->rl_no) . '.pdf';

        return $pdf->stream($safeFilename);
    }

    // 5. LIST BY STATUS (FIXED: AMBIGUOUS COLUMN & DYNAMIC DB NAME)
    public function listByStatus(Request $request, $status)
    {
        $validStatuses = [
            'DRAFT', 'ON_PROGRESS', 'PARTIALLY_APPROVED',
            'APPROVED', 'REJECTED', 'WAITING_SUPPLY', 'COMPLETED'
        ];

        $statusUpper = strtoupper($status);
        if (!in_array($statusUpper, $validStatuses)) abort(404);

        $user = Auth::user();

        // 1. MULAI QUERY (Select table utama agar ID tidak tertimpa)
        $query = RequisitionLetter::select('requisition_letters.*')
                    ->with(['requester.department', 'company']);

        // 2. FILTER STATUS (Pake nama tabel biar aman)
        $query->where('requisition_letters.status_flow', $statusUpper);

        // 3. FILTER ROLE
        $isApprover = ($user->position && in_array($user->position->position_name, ['Manager', 'Director']));
        $isSuperAdmin = ($user->position && $user->position->position_name === 'Super Admin');

        if ($isSuperAdmin) {
            // Show All
        } elseif ($isApprover) {
            // [FIX] Tambahkan 'requisition_letters.' untuk menghindari ambigu
            $query->where('requisition_letters.company_id', $user->company_id);

            // Ambil ID bawahan/rekan se-departemen (Cross-DB Safe)
            // Gunakan koneksi dari model User untuk mendapatkan nama DB yang benar
            $userModel = new \App\Models\User();
            $userDb = $userModel->getConnection()->getDatabaseName();

            $deptColleagues = DB::table($userDb . '.tbl_employee')
                                ->where('department_id', $user->department_id)
                                ->pluck('employee_id')
                                ->toArray();

            // [FIX] Tambahkan 'requisition_letters.'
            $query->whereIn('requisition_letters.requester_id', $deptColleagues);
        } else {
            // [FIX] Tambahkan 'requisition_letters.'
            $query->where('requisition_letters.requester_id', $user->employee_id);
        }

        // 4. SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('requisition_letters.rl_no', 'like', "%$search%")
                  ->orWhere('requisition_letters.subject', 'like', "%$search%");
            });
        }

        // 5. DATE FILTER
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('requisition_letters.request_date', [$request->start_date, $request->end_date]);
        }

        // 6. SORTING (FIXED: DYNAMIC TABLE NAME)
        // Ambil nama database dan tabel user secara dinamis dari Model User
        $userModel = new \App\Models\User();
        $userTable = $userModel->getTable(); // tbl_employee
        $userDb = $userModel->getConnection()->getDatabaseName(); // Nama Database Asli
        $fullUserTable = $userDb . '.' . $userTable; // Hasil: db_master.tbl_employee

        // Join dinamis (bukan hardcode)
        $query->leftJoin($fullUserTable . ' as u', 'requisition_letters.requester_id', '=', 'u.employee_id');

        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('dir', 'desc');

        switch ($sortColumn) {
            case 'rl_no':
                $query->orderBy('requisition_letters.rl_no', $sortDirection);
                break;
            case 'request_date':
                $query->orderBy('requisition_letters.request_date', $sortDirection);
                break;
            case 'requester_name':
                $query->orderBy('u.full_name', $sortDirection);
                break;
            case 'subject':
                $query->orderBy('requisition_letters.subject', $sortDirection);
                break;
            default:
                $query->orderBy('requisition_letters.created_at', 'desc');
                break;
        }

        // 7. PAGINATION
        $requisitions = $query->paginate(10)->withQueryString();

        return view('requisitions.index', compact('requisitions', 'status', 'statusUpper'));
    }

    // 6. SUBMIT DRAFT
    public function submitDraft($id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        if ($rl->status_flow != 'DRAFT') {
            return back()->with('error', 'Dokumen ini sudah diproses.');
        }
        if (Auth::user()->employee_id != $rl->requester_id) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $notifData = null;

        DB::transaction(function () use ($rl, &$notifData) {
            $rl->update(['status_flow' => 'ON_PROGRESS']);

            $manager = User::where('company_id', $rl->company_id)
                ->where('department_id', $rl->requester->department_id)
                ->whereHas('position', function($q) {
                    $q->where('position_name', 'Manager');
                })->first();

            if (!$manager) {
                $manager = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();
            }

            if ($manager) {
                $exists = ApprovalQueue::where('rl_id', $rl->id)->exists();
                if (!$exists) {
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $manager->employee_id,
                        'level_order' => 1,
                        'status' => 'PENDING'
                    ]);

                    $notifData = [
                        'target' => $manager,
                        'rl' => $rl
                    ];
                }
            }
        });

        $waStatusMsg = "";

        if ($notifData) {
            if (!empty($notifData['target']->phone)) {
                $target = $notifData['target'];
                $link = route('requisitions.show', $rl->id);

                $pesan = "Halo Bpk/Ibu *{$target->full_name}*,\n\n";
                $pesan .= "Draft RL berikut telah diajukan dan menunggu persetujuan Anda.\n\n";
                $pesan .= "📝 No RL: *{$rl->rl_no}*\n";
                $pesan .= "👤 Requester: {$rl->requester->full_name}\n";
                $pesan .= "📄 Subject: {$rl->subject}\n\n";
                $pesan .= "Klik link berikut untuk Approval:\n{$link}\n\n";
                $pesan .= "_RL Monitoring System_";

                $result = WaService::send($target->phone, $pesan);

                if ($result['status']) {
                    $waStatusMsg = " & " . $result['msg'];
                } else {
                    $waStatusMsg = " (Namun WA Gagal: " . $result['msg'] . ")";
                }
            } else {
                $waStatusMsg = " (Info: Notifikasi WA tidak dikirim karena No. HP Manager belum diisi)";
            }
        }
        return redirect()->route('dashboard')->with('success', 'Draft diajukan' . $waStatusMsg . '!');
    }

    // 7. PREVIEW TEMP
    public function previewTemp(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'required|string',
            'items' => 'required|array',
        ]);

        $user = Auth::user()->load(['department', 'position']);
        $company = \App\Models\Company::find($user->company_id);

        $companyCode = $company->company_code ?? 'GEN';
        $deptFull = $user->department->department_name ?? 'GEN';
        $deptParts = preg_split('/[\s(]/', $deptFull);
        $deptCode = strtoupper($deptParts[0]);

        $month = date('m', strtotime($request->request_date));
        $year = date('Y', strtotime($request->request_date));

        $count = RequisitionLetter::where('company_id', $user->company_id)
            ->whereYear('request_date', $year)
            ->whereMonth('request_date', $month)->count();

        $nextNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $realDraftNo = "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$nextNo}";

        $rl = new RequisitionLetter();
        $rl->rl_no = $realDraftNo;
        $rl->request_date = $request->request_date;
        $rl->subject = $request->subject;
        $rl->to_department = 'Purchasing / Procurement';
        $rl->priority = $request->priority;
        $rl->required_date = $request->required_date;
        $rl->remark = $request->remark;
        $rl->status_flow = 'DRAFT';

        $rl->setRelation('requester', $user);
        $rl->setRelation('company', $company);
        $rl->setRelation('approvalQueues', collect([]));

        $items = collect();
        if($request->has('items')){
            foreach ($request->items as $itemData) {
                $item = new \App\Models\RequisitionItem($itemData);
                $items->push($item);
            }
        }
        $rl->setRelation('items', $items);

        $manager = User::where('company_id', $user->company_id)
            ->where('department_id', $user->department_id)
            ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })->first();

        if (!$manager) {
            $manager = User::where('company_id', $user->company_id)
                ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })->first();
        }
        $director = User::where('company_id', $user->company_id)
            ->whereHas('position', function($q){ $q->where('position_name', 'Director'); })->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }

    // 8. REVISE
    public function revise($id)
    {
        $oldRl = RequisitionLetter::with('items')->findOrFail($id);
        if ($oldRl->requester_id != Auth::user()->employee_id) abort(403);
        $newNumber = RequisitionLetter::generateNumber();
        return view('requisitions.create', compact('newNumber', 'oldRl'));
    }

    // 9. DEPARTMENT ACTIVITY
    public function departmentActivity()
    {
        $user = Auth::user();
        $teamMemberIds = User::where('department_id', $user->department_id)
            ->where('company_id', $user->company_id)
            ->pluck('employee_id');

        $requisitions = RequisitionLetter::with(['requester.department', 'items'])
            ->whereIn('requester_id', $teamMemberIds)
            ->where('status_flow', '!=', 'DRAFT')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $statusUpper = 'ACTIVITIES';
        return view('requisitions.department', compact('requisitions', 'statusUpper'));
    }

    // 10. UPLOAD PARTIAL
    public function uploadPartial(Request $request, $id)
    {
        $request->validate([
            'file_partial' => 'required|mimes:pdf|max:5120',
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('file_partial')) {
            $path = $request->file('file_partial')->store('uploads/rl_documents', 'public');
            $rl->attachment_partial = $path;
            $rl->status_flow = 'ON_PROGRESS';
            $rl->save();
        }

        $this->generateManagerQueue($rl);
        $this->sendWaToManager($rl);

        return back()->with('success', 'Dokumen Tahap 1 berhasil diupload! Menunggu validasi Manager.');
    }

    // 11. UPLOAD FINAL
    public function uploadFinal(Request $request, $id)
    {
        $request->validate([
            'file_final' => 'required|mimes:pdf|max:5120',
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('file_final')) {
            $path = $request->file('file_final')->store('uploads/rl_documents', 'public');
            $rl->attachment_final = $path;
            $rl->status_flow = 'WAITING_SUPPLY';
            $rl->save();
        }

        return back()->with('success', 'Dokumen Final diterima. Status: Waiting Supply (Menunggu Barang).');
    }

    // 12. UPLOAD EVIDENCE
    public function uploadEvidence(Request $request, $id)
    {
        $request->validate([
            'evidence_photo' => 'required|image|max:5120',
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('evidence_photo')) {
            $path = $request->file('evidence_photo')->store('uploads/evidence', 'public');
            $rl->evidence_photo = $path;
            $rl->status_flow = 'COMPLETED';
            $rl->save();
        }

        return back()->with('success', 'Bukti barang diterima. Tiket Selesai (COMPLETED).');
    }

    // PRIVATE HELPER 1
    private function generateManagerQueue($rl)
    {
        $manager = User::where('company_id', $rl->company_id)
            ->where('department_id', $rl->requester->department_id)
            ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();

        if (!$manager) {
            $manager = User::where('company_id', $rl->company_id)
                ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();
        }

        if ($manager) {
            $exists = ApprovalQueue::where('rl_id', $rl->id)->where('level_order', 1)->exists();
            if (!$exists) {
                ApprovalQueue::create([
                    'rl_id' => $rl->id,
                    'approver_id' => $manager->employee_id,
                    'level_order' => 1,
                    'status' => 'PENDING'
                ]);
            }
        }
        return $manager;
    }

    // PRIVATE HELPER 2
    private function sendWaToManager($rl)
    {
        $queue = ApprovalQueue::where('rl_id', $rl->id)->where('level_order', 1)->first();
        if ($queue && $queue->approver->phone) {
            $link = route('requisitions.show', $rl->id);
            $msg = "Halo *{$queue->approver->full_name}*,\n\nRequester telah mengupload dokumen RL fisik (TTD Basah).\nNo: *{$rl->rl_no}*\n\nMohon cek validitas dokumen dan klik APPROVE di sistem:\n{$link}";

            WaService::send($queue->approver->phone, $msg);
        }
    }
}
