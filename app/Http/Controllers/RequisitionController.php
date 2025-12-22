<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\RequisitionItem;
use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WaService; // Ensure this service exists

class RequisitionController extends Controller
{
    // 1. CREATE FORM
    public function create()
    {
        // Get Master Items
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();
        // Generate Auto Number for display
        $newRlNumber = RequisitionLetter::generateNumber();
        $user = Auth::user();

        // Check for old RL if revision (handled in view usually, but good to check)
        $oldRl = null;
        if (request()->has('revise_id')) {
            $oldRl = RequisitionLetter::with('items')->find(request()->revise_id);
        }

        return view('requisitions.create', [
            'newNumber' => $newRlNumber,
            'user' => $user,
            'masterItems' => $masterItems,
            'oldRl' => $oldRl
        ]);
    }

    // 2. STORE DATA (SAVES AS DRAFT ONLY)
    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'subject' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uom' => 'required|string',
        ]);

        $rlId = null;

        DB::transaction(function () use ($request, &$rlId) {
            $user = Auth::user();

            // ALWAYS SAVE AS DRAFT FIRST
            // This ensures the RL Number matches the printed PDF later.
            $status = 'DRAFT';
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

            $rlId = $rl->id;

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
        });

        // Redirect to SHOW page so user can Print -> Sign -> Upload
        return redirect()->route('requisitions.show', $rlId)
            ->with('success', 'Draft Created Successfully! Please download the PDF, sign it, and upload it to proceed.');
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

    // 4. SUBMIT DRAFT (THE TRIGGER)
    public function submitDraft($id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        // Security Checks
        if ($rl->requester_id != Auth::id()) {
            return back()->with('error', 'Unauthorized access.');
        }
        if ($rl->status_flow != 'DRAFT') {
            return back()->with('error', 'Document is already being processed.');
        }

        // HARD VALIDATION: Must upload file first
        if (!$rl->attachment_partial) {
            return back()->with('error', 'Submission Failed: You must upload the signed document (Requester & Manager Signature) first.');
        }

        $notifData = null;

        DB::transaction(function () use ($rl, &$notifData) {
            // 1. Update Status
            $rl->update([
                'status_flow' => 'ON_PROGRESS',
                'request_date' => now() // Update date to actual submission
            ]);

            // 2. Find Manager (Approval 1)
            $manager = User::where('company_id', $rl->company_id)
                ->where('department_id', $rl->requester->department_id)
                ->whereHas('position', function($q) {
                    $q->where('position_name', 'Manager');
                })->first();

            // Fallback: General Manager of Company
            if (!$manager) {
                $manager = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();
            }

            // 3. Create Approval Queue
            if ($manager) {
                // Check if queue already exists to prevent duplicates
                $exists = ApprovalQueue::where('rl_id', $rl->id)->where('level_order', 1)->exists();

                if (!$exists) {
                    ApprovalQueue::create([
                        'rl_id' => $rl->id,
                        'approver_id' => $manager->employee_id,
                        'level_order' => 1,
                        'status' => 'PENDING'
                    ]);

                    $notifData = [
                        'target' => $manager,
                        'rl' => $rl,
                        'sender' => $rl->requester->full_name
                    ];
                }
            }
        });

        // 4. Send Notification
        $waStatusMsg = "";
        if ($notifData && !empty($notifData['target']->phone)) {
            $target = $notifData['target'];
            $link = route('requisitions.show', $rl->id);

            $msg = "Hello *{$target->full_name}*,\n\n";
            $msg .= "A new Requisition Letter (RL) is waiting for your approval.\n\n";
            $msg .= "📝 RL No: *{$rl->rl_no}*\n";
            $msg .= "👤 Requester: {$notifData['sender']}\n";
            $msg .= "📄 Subject: {$rl->subject}\n\n";
            $msg .= "Please review the document here:\n{$link}\n\n";
            $msg .= "_RL Monitoring System_";

            $result = WaService::send($target->phone, $msg);
            $waStatusMsg = $result['status'] ? "" : " (WA Failed: " . $result['msg'] . ")";
        }

        return redirect()->back()->with('success', 'Request submitted successfully to Manager!' . $waStatusMsg);
    }

    // 5. UPLOAD PARTIAL (FILE STORAGE ONLY)
    public function uploadPartial(Request $request, $id)
    {
        $request->validate([
            'file_partial' => 'required|mimes:pdf|max:5120', // Max 5MB
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('file_partial')) {
            $path = $request->file('file_partial')->store('uploads/rl_documents', 'public');
            $rl->attachment_partial = $path;
            $rl->save();
        }

        return back()->with('success', 'Document uploaded successfully! Please click "Submit to Manager" to proceed.');
    }

    // 6. UPLOAD FINAL (FILE STORAGE ONLY)
    public function uploadFinal(Request $request, $id)
    {
        $request->validate([
            'file_final' => 'required|mimes:pdf|max:5120',
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('file_final')) {
            $path = $request->file('file_final')->store('uploads/rl_documents', 'public');
            $rl->attachment_final = $path;
            $rl->save();
        }

        return back()->with('success', 'Final Document uploaded successfully!');
    }

    // 7. UPLOAD EVIDENCE
    public function uploadEvidence(Request $request, $id)
    {
        $request->validate([
            'evidence_photo' => 'required|image|max:5120',
        ]);

        $rl = RequisitionLetter::findOrFail($id);

        if ($request->hasFile('evidence_photo')) {
            $path = $request->file('evidence_photo')->store('uploads/evidence', 'public');
            $rl->evidence_photo = $path;
            $rl->status_flow = 'COMPLETED'; // Closing Ticket
            $rl->save();
        }

        return back()->with('success', 'Evidence uploaded. Ticket Completed.');
    }

    // 8. PRINT PDF
    public function printPdf($id)
    {
        $rl = RequisitionLetter::with([
            'items',
            'requester.department',
            'company',
            'approvalQueues.approver.position'
        ])->findOrFail($id);

        // 1. Find Manager (Plan)
        $manager = User::where('company_id', $rl->company_id)
            ->where('department_id', $rl->requester->department_id)
            ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();

        if (!$manager) {
            $manager = User::where('company_id', $rl->company_id)
                ->whereHas('position', function($q) { $q->where('position_name', 'Manager'); })->first();
        }

        // 2. Find Director (Plan)
        $directorRole = 'Director';
        if ($rl->company && $rl->company->company_code == 'ASM') {
            $directorRole = 'Managing Director';
        }

        $director = User::where('company_id', $rl->company_id)
            ->whereHas('position', function($q) use ($directorRole) {
                $q->where('position_name', $directorRole);
            })->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        // Clean Filename
        $cleanNo = str_replace(' ', '', $rl->rl_no);
        $safeFilename = str_replace(['/', '\\'], '-', $cleanNo) . '.pdf';

        return $pdf->stream($safeFilename);
    }

    // 9. PREVIEW TEMP (FOR DRAFTING)
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

        // Clean Dept Code Logic
        $deptFull = $user->department->department_name ?? 'GEN';
        $deptCode = 'GEN';

        if (stripos($deptFull, 'Information Technology') !== false) $deptCode = 'IT';
        elseif (stripos($deptFull, 'Human Resource') !== false) $deptCode = 'HR';
        elseif (stripos($deptFull, 'General Affair') !== false) $deptCode = 'GA';
        elseif (stripos($deptFull, 'Finance') !== false) $deptCode = 'FIN';
        elseif (stripos($deptFull, 'Procurement') !== false) $deptCode = 'PRC';
        else {
            $deptParts = preg_split('/[\s(]/', $deptFull);
            $deptCode = strtoupper($deptParts[0]);
        }

        // Aggressive Cleaning
        $deptCode = preg_replace('/\s+/', '', $deptCode);
        $companyCode = preg_replace('/\s+/', '', $companyCode);

        // Generate Dummy Number
        $month = date('m', strtotime($request->request_date));
        $year = date('Y', strtotime($request->request_date));
        $count = RequisitionLetter::where('company_id', $user->company_id)
            ->whereYear('request_date', $year)
            ->whereMonth('request_date', $month)->count();
        $nextNo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $realDraftNo = "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$nextNo}";

        // Temporary Object
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

        // Managers & Directors (Same Logic as printPdf)
        $manager = User::where('company_id', $user->company_id)
            ->where('department_id', $user->department_id)
            ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })->first();

        if (!$manager) {
            $manager = User::where('company_id', $user->company_id)
                ->whereHas('position', function($q){ $q->where('position_name', 'Manager'); })->first();
        }

        $directorRole = ($companyCode == 'ASM') ? 'Managing Director' : 'Director';
        $director = User::where('company_id', $user->company_id)
            ->whereHas('position', function($q) use ($directorRole) {
                $q->where('position_name', $directorRole);
            })->first();

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }

    // 10. REVISE
    public function revise($id)
    {
        $oldRl = RequisitionLetter::with('items')->findOrFail($id);
        if ($oldRl->requester_id != Auth::user()->employee_id) abort(403);

        $newNumber = RequisitionLetter::generateNumber();
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();
        $user = Auth::user();

        return view('requisitions.create', compact('newNumber', 'oldRl', 'masterItems', 'user'));
    }

    // 11. LIST BY STATUS (DYNAMIC)
    public function listByStatus(Request $request, $status)
    {
        $validStatuses = [
            'DRAFT', 'ON_PROGRESS', 'PARTIALLY_APPROVED',
            'APPROVED', 'REJECTED', 'WAITING_SUPPLY', 'COMPLETED'
        ];

        $statusUpper = strtoupper($status);
        if (!in_array($statusUpper, $validStatuses)) abort(404);

        $user = Auth::user();
        $query = RequisitionLetter::select('requisition_letters.*')
                    ->with(['requester.department', 'company']);

        $query->where('requisition_letters.status_flow', $statusUpper);

        // Role Filter
        $isApprover = ($user->position && in_array($user->position->position_name, ['Manager', 'Director', 'Managing Director', 'Deputy Managing Director', 'General Manager']));
        $isSuperAdmin = ($user->position && $user->position->position_name === 'Super Admin');

        if ($isSuperAdmin) {
            // All
        } elseif ($isApprover) {
            $query->where('requisition_letters.company_id', $user->company_id);

            // Dynamic DB Connection for Cross-Join
            $userModel = new \App\Models\User();
            $userDb = $userModel->getConnection()->getDatabaseName();

            $deptColleagues = DB::table($userDb . '.tbl_employee')
                                ->where('department_id', $user->department_id)
                                ->pluck('employee_id')
                                ->toArray();

            // Show dept colleagues OR self
            $query->whereIn('requisition_letters.requester_id', array_merge($deptColleagues, [$user->employee_id]));
        } else {
            $query->where('requisition_letters.requester_id', $user->employee_id);
        }

        // Search & Filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('requisition_letters.rl_no', 'like', "%{$request->search}%")
                  ->orWhere('requisition_letters.subject', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('requisition_letters.request_date', [$request->start_date, $request->end_date]);
        }

        // Sorting
        $query->orderBy('requisition_letters.created_at', 'desc');

        $requisitions = $query->paginate(10)->withQueryString();

        return view('requisitions.index', compact('requisitions', 'status', 'statusUpper'));
    }

    // 12. DEPARTMENT ACTIVITY
    public function departmentActivity()
    {
        $user = Auth::user();

        $userModel = new \App\Models\User();
        $userDb = $userModel->getConnection()->getDatabaseName();

        $teamMemberIds = DB::table($userDb . '.tbl_employee')
            ->where('department_id', $user->department_id)
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
}
