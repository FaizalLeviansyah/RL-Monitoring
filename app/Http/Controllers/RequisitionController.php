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

class RequisitionController extends Controller
{
    // 1. CREATE FORM
    public function create()
    {
        $newNumber = RequisitionLetter::generateNumber();
        return view('requisitions.create', compact('newNumber'));
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

        DB::transaction(function () use ($request) {
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
                }
            }
        });

        $msg = ($request->action === 'draft') ? 'Draft berhasil disimpan!' : 'Permintaan berhasil diajukan!';
        return redirect()->route('dashboard')->with('success', $msg);
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

    // 4. PRINT PDF (REAL DATA)
    public function printPdf($id)
    {
        $rl = RequisitionLetter::with([
            'items',
            'requester.department',
            'company',
            'approvalQueues.approver.position'
        ])->findOrFail($id);

        // --- CARI PEJABAT (Agar Tanda Tangan Muncul) ---
        // 1. Manager
        $manager = User::where('company_id', $rl->company_id)
                    ->where('department_id', $rl->requester->department_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();

        // Fallback Manager
        if (!$manager) {
            $manager = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();
        }

        // 2. Director
        $director = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Director');
                    })->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        $safeFilename = 'RL-' . str_replace(['/', '\\'], '-', $rl->rl_no) . '.pdf';

        return $pdf->stream($safeFilename);
    }

    // 5. LIST BY STATUS
    public function listByStatus($status)
    {
        $validStatuses = ['DRAFT', 'ON_PROGRESS', 'APPROVED', 'REJECTED'];
        $statusUpper = strtoupper($status);

        if (!in_array($statusUpper, $validStatuses)) {
            abort(404);
        }

        $user = Auth::user();
        $query = RequisitionLetter::with(['requester.department', 'company'])
                    ->where('status_flow', $statusUpper)
                    ->orderBy('created_at', 'desc');

        $isApprover = false;
        if ($user->position && in_array($user->position->position_name, ['Manager', 'Director'])) {
            $isApprover = true;
        }

        if (!$isApprover) {
             $query->where('requester_id', $user->employee_id);
        }

        $requisitions = $query->paginate(10);
        return view('requisitions.index', compact('requisitions', 'status', 'statusUpper'));
    }

    // 6. SUBMIT DRAFT
    public function submitDraft($id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        if ($rl->status_flow != 'DRAFT') {
            return back()->with('error', 'Dokumen ini sudah diajukan atau diproses.');
        }
        if (Auth::user()->employee_id != $rl->requester_id) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        DB::transaction(function () use ($rl) {
            $rl->update(['status_flow' => 'ON_PROGRESS']);

            $manager = User::where('company_id', $rl->company_id)
                        ->whereHas('position', function($q) {
                            $q->where('position_name', 'Manager');
                        })->first();

            if ($manager) {
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

    // 7. PREVIEW TEMP (DUMMY DATA) - [BAGIAN INI YANG SAYA LENGKAPI]
    public function previewTemp(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'required|string',
            'items' => 'required|array',
        ]);

        $user = Auth::user()->load(['department', 'position']);
        $company = \App\Models\Company::find($user->company_id);

        // --- A. GENERATE NOMOR PREVIEW ---
        $companyCode = $company->company_code ?? 'GEN';
        $deptFull = $user->department->department_name ?? 'GEN';
        $deptParts = preg_split('/[\s(]/', $deptFull);
        $deptCode = strtoupper($deptParts[0]);

        $month = date('m', strtotime($request->request_date));
        $year = date('Y', strtotime($request->request_date));

        $count = RequisitionLetter::where('company_id', $user->company_id)
                    ->whereYear('request_date', $year)
                    ->whereMonth('request_date', $month)
                    ->count();

        $nextNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        $realDraftNo = "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$nextNo}";

        // --- B. BUAT OBJECT DUMMY ($rl) ---
        // (Ini yang hilang di kode Anda sebelumnya)
        $rl = new RequisitionLetter();
        $rl->rl_no = $realDraftNo;
        $rl->request_date = $request->request_date;
        $rl->subject = $request->subject;
        $rl->to_department = 'Purchasing / Procurement';
        $rl->priority = $request->priority;
        $rl->required_date = $request->required_date;
        $rl->remark = $request->remark;
        $rl->status_flow = 'DRAFT';

        // Set Relasi Manual (karena belum masuk DB)
        $rl->setRelation('requester', $user);
        $rl->setRelation('company', $company);
        $rl->setRelation('approvalQueues', collect([])); // Kosongkan approval queue

        // Buat Dummy Items
        $items = collect();
        if($request->has('items')){
            foreach ($request->items as $itemData) {
                $item = new \App\Models\RequisitionItem($itemData);
                $items->push($item);
            }
        }
        $rl->setRelation('items', $items);

        // --- C. CARI PEJABAT (Sama seperti printPdf) ---
        $manager = User::where('company_id', $user->company_id)
                    ->where('department_id', $user->department_id)
                    ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })
                    ->first();

        // Fallback Manager
        if (!$manager) {
            $manager = User::where('company_id', $user->company_id)
                    ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })
                    ->first();
        }

        $director = User::where('company_id', $user->company_id)
                    ->whereHas('position', function($q){ $q->where('position_name', 'Director'); })
                    ->first();

        // Load View PDF
        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }

    // 8. REVISE
    public function revise($id)
    {
        $oldRl = RequisitionLetter::with('items')->findOrFail($id);
        if ($oldRl->requester_id != Auth::user()->employee_id) {
            abort(403);
        }
        $newNumber = RequisitionLetter::generateNumber();
        return view('requisitions.create', compact('newNumber', 'oldRl'));
    }

    // 9. DEPARTMENT ACTIVITY (CROSS DB FIX)
    public function departmentActivity()
    {
        $user = Auth::user();

        // LANGKAH 1: Ambil semua ID teman satu departemen
        $teamMemberIds = User::where('department_id', $user->department_id)
                             ->where('company_id', $user->company_id)
                             ->pluck('employee_id');

        // LANGKAH 2: Ambil surat (Gunakan whereIn agar aman beda DB)
        $requisitions = RequisitionLetter::with(['requester.department', 'items'])
            ->whereIn('requester_id', $teamMemberIds)
            ->where('status_flow', '!=', 'DRAFT')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $statusUpper = 'ACTIVITIES';

        return view('requisitions.department', compact('requisitions', 'statusUpper'));
    }
}
