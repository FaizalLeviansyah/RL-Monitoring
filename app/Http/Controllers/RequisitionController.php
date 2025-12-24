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
use App\Services\WaService; // Pastikan service ini ada

class RequisitionController extends Controller
{
    // =========================================================================
    // 1. VIEW & LIST DATA
    // =========================================================================

    public function index()
    {
        return $this->listByStatus(request(), 'DRAFT'); // Default view ke Draft/All
    }

public function listByStatus(Request $request, $status = 'DRAFT')
    {
        $statusUpper = strtoupper($status);
        $user = Auth::user();

        // Mapping URL ke Status DB
        if ($statusUpper == 'WAITING_APPROVAL') $statusUpper = 'ON_PROGRESS';
        if ($statusUpper == 'WAITING_DIRECTOR') $statusUpper = 'PARTIALLY_APPROVED';

        // 1. Query Dasar
        $query = RequisitionLetter::with(['requester.department', 'items', 'approvalQueues'])
                    ->orderBy('created_at', 'desc');

        // 2. Logic Role & Permission Scope
        if ($user->position->position_name === 'Super Admin') {
            // Super Admin: Lihat Semua
        }
        elseif (in_array($user->position->position_name, ['Manager', 'Director', 'Managing Director', 'General Manager'])) {

            // FIX ERROR TABLE NOT FOUND:
            // Ambil ID bawahan secara terpisah agar aman dari Cross-Database Join
            $teamIds = User::where('department_id', $user->department_id)
                           ->where('company_id', $user->company_id)
                           ->pluck('employee_id')
                           ->toArray();

            // APPROVER: Lihat surat sendiri + Queue + Surat Tim
            $query->where(function($q) use ($user, $teamIds) {
                $q->where('requester_id', $user->employee_id)
                  ->orWhereHas('approvalQueues', function($aq) use ($user) {
                      $aq->where('approver_id', $user->employee_id);
                  })
                  // Gunakan whereIn (Aman), bukan whereHas
                  ->orWhereIn('requester_id', $teamIds);
            });
        }
        else {
            // STAFF: Hanya lihat surat miliknya sendiri
            $query->where('requester_id', $user->employee_id);
        }

        // 3. Filter Status Utama (Kecuali user pilih "My Requests" / All)
        if ($statusUpper !== 'ALL' && $statusUpper !== 'DRAFTS') {
             $query->where('status_flow', $statusUpper);
        } elseif ($statusUpper == 'DRAFTS') {
             $query->where('status_flow', 'DRAFT');
        }

        // 4. Search & Filter Date
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('rl_no', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('request_date', [$request->start_date, $request->end_date]);
        }

        $requisitions = $query->paginate(10)->withQueryString();
        return view('requisitions.index', compact('requisitions', 'statusUpper'));
    }

    public function show($id)
    {
        $requisition = RequisitionLetter::with([
            'requester.department',
            'requester.position',
            'items',
            'approvalQueues.approver' // Load history approval
        ])->findOrFail($id);

        return view('requisitions.show', compact('requisition'));
    }

public function departmentActivity()
    {
        $user = Auth::user();

        // 1. Cari ID teman se-departemen & se-PT
        $teamMemberIds = User::where('department_id', $user->department_id)
                             ->where('company_id', $user->company_id)
                             ->pluck('employee_id');

        // 2. Ambil semua RL milik mereka (kecuali Draft, karena Draft itu privat)
        $requisitions = RequisitionLetter::with(['requester.department', 'items'])
            ->whereIn('requester_id', $teamMemberIds)
            ->where('status_flow', '!=', 'DRAFT') // Draft tidak boleh diintip
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $statusUpper = 'ACTIVITIES';
        return view('requisitions.department', compact('requisitions', 'statusUpper'));
    }

    // =========================================================================
    // 2. CREATE, EDIT & ACTION
    // =========================================================================

    public function create()
    {
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();
        $newRlNumber = RequisitionLetter::generateNumber();
        $user = Auth::user();

        // Handle Revision logic from old code
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

    public function store(Request $request)
    {
        $request->validate([
            'required_date' => 'required|date',
            'subject' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uom' => 'required|string',
        ]);

        $rlId = null;

        DB::transaction(function () use ($request, &$rlId) {
            $user = Auth::user();
            $rlNo = RequisitionLetter::generateNumber();

            // Create Header (Always DRAFT first)
            $rl = RequisitionLetter::create([
                'company_id' => $user->company_id,
                'requester_id' => $user->employee_id,
                'rl_no' => $rlNo,
                'request_date' => now(),
                'status_flow' => 'DRAFT',
                'subject' => $request->subject,
                'to_department' => 'Purchasing / Procurement',
                'priority' => $request->priority,
                'required_date' => $request->required_date,
                'remark' => $request->remark,
            ]);

            $rlId = $rl->id;

            // Create Items
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

        return redirect()->route('requisitions.show', $rlId)
            ->with('success', 'Draft Created! Please review and click "Submit" to proceed.');
    }

    public function edit($id)
    {
        $requisition = RequisitionLetter::with('items')->findOrFail($id);

        // Security Check
        if (!in_array($requisition->status_flow, ['DRAFT', 'REJECTED'])) {
            return redirect()->back()->with('error', 'Cannot edit document currently in progress.');
        }

        // TAMBAHAN WAJIB: Load Master Item agar dropdown di form edit berfungsi
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();

        return view('requisitions.edit', compact('requisition', 'masterItems'));
    }

    public function update(Request $request, $id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        if (!in_array($rl->status_flow, ['DRAFT', 'REJECTED'])) {
            return abort(403);
        }

        $request->validate([
            'required_date' => 'required|date',
            'subject'       => 'required|string|max:255',
            'items'         => 'required|array|min:1',
        ]);

        // Update Header
        $rl->update([
            'subject'       => $request->subject,
            'required_date' => $request->required_date,
            'priority'      => $request->priority,
            'remark'        => $request->remark,
            // Jika REJECTED -> ON_PROGRESS (Auto Resubmit)
            'status_flow'   => ($rl->status_flow == 'REJECTED') ? 'ON_PROGRESS' : $rl->status_flow,
        ]);

        // Smart Update Items
        $submittedItemIds = collect($request->items)->pluck('id')->filter()->toArray();
        $rl->items()->whereNotIn('id', $submittedItemIds)->delete();

        foreach ($request->items as $itemData) {
            $rl->items()->updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                [
                    'item_name'   => $itemData['item_name'],
                    'qty'         => $itemData['qty'],
                    'uom'         => $itemData['uom'],
                    'description' => $itemData['description'] ?? null,
                    'status_item' => 'WAITING'
                ]
            );
        }

        // Jika status berubah jadi ON_PROGRESS (Resubmit), generate ulang queue
        if ($rl->wasChanged('status_flow') && $rl->status_flow == 'ON_PROGRESS') {
             $this->generateApprovalQueue($rl);
        }

        return redirect()->route('requisitions.show', $rl->id)
                         ->with('success', 'Requisition updated successfully!');
    }

    // 7. SUBMIT (PENGGANTI submitDraft)
    // Ini adalah tombol trigger untuk mengubah DRAFT -> ON_PROGRESS
public function submit($id)
    {
        $rl = RequisitionLetter::findOrFail($id);

        if ($rl->requester_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($rl->status_flow !== 'DRAFT') {
            return back()->with('error', 'Document is already submitted.');
        }

        // 1. Update Status
        $rl->update([
            'status_flow' => 'ON_PROGRESS',
            'request_date' => now()
        ]);

        // 2. Generate Antrian Approval
        $this->generateApprovalQueue($rl);

        // 3. Notifikasi WA (PROFESSIONAL & INFORMATIVE)
        try {
            // Cari Manager (Approval Level 1) untuk dikirim WA
            $managerQueue = $rl->approvalQueues()->where('level_order', 1)->first();

            if ($managerQueue && $managerQueue->approver->phone) {
                $managerName = $managerQueue->approver->full_name;
                $requesterName = $rl->requester->full_name;
                $rlNo = $rl->rl_no;
                $subject = $rl->subject;
                $link = route('requisitions.show', $rl->id); // Link langsung ke detail surat

                // Format Pesan WhatsApp
                $msg = "*APPROVAL REQUIRED*\n\n";
                $msg .= "Dear Mr/Ms. {$managerName},\n";
                $msg .= "A new Requisition Letter has been submitted and requires your approval.\n\n";
                $msg .= "📄 *No:* {$rlNo}\n";
                $msg .= "👤 *Requester:* {$requesterName}\n";
                $msg .= "📝 *Subject:* \"{$subject}\"\n\n";
                $msg .= "Please click the link below to review and approve:\n";
                $msg .= "🔗 {$link}\n\n";
                $msg .= "_This is an automated message from RL Monitoring System._";

                WaService::send($managerQueue->approver->phone, $msg);
            }
        } catch (\Exception $e) {
            // Silent fail (jangan sampai error WA membatalkan submit)
            \Illuminate\Support\Facades\Log::error("WA Error: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Requisition submitted! Notification sent to Manager.');
    }

    public function revise($id)
    {
        // Fungsi lama Bapak untuk revisi (kloning data)
        $oldRl = RequisitionLetter::with('items')->findOrFail($id);
        if ($oldRl->requester_id != Auth::user()->employee_id) abort(403);

        $newNumber = RequisitionLetter::generateNumber();
        $masterItems = \App\Models\MasterItem::orderBy('item_name', 'asc')->get();
        $user = Auth::user();

        return view('requisitions.create', compact('newNumber', 'oldRl', 'masterItems', 'user'));
    }

    // =========================================================================
    // 3. APPROVAL & PDF LOGIC
    // =========================================================================

public function approve($id)
    {
        $user = Auth::user();
        // Load relasi lengkap biar data WA lengkap
        $rl = RequisitionLetter::with(['requester', 'approvalQueues.approver'])->findOrFail($id);

        // 1. Cari antrian milik user ini
        $queue = $rl->approvalQueues()
                    ->where('approver_id', $user->employee_id)
                    ->where('status', 'PENDING')
                    ->first();

        if (!$queue) {
            return back()->with('error', 'No pending approval found for you.');
        }

        // 2. Update Status Queue User Ini jadi APPROVED
        $queue->update([
            'status' => 'APPROVED',
            'updated_at' => now()
        ]);

        // 3. LOGIKA LANJUTAN (BERJENJANG)

        // --- SKENARIO A: MANAGER (Level 1) APPROVE ---
        if ($queue->level_order == 1) {
            $rl->update(['status_flow' => 'PARTIALLY_APPROVED']);

            // -> KIRIM WA KE DIREKTUR (Level 2)
            try {
                $nextQueue = $rl->approvalQueues()->where('level_order', 2)->first();
                if ($nextQueue && $nextQueue->approver->phone) {
                    $director = $nextQueue->approver;
                    $link = route('requisitions.show', $rl->id);

                    $msg = "*APPROVAL REQUIRED (Final Stage)*\n\n";
                    $msg .= "Dear Mr/Ms. {$director->full_name},\n";
                    $msg .= "Manager has approved RL No: *{$rl->rl_no}*. It now requires your FINAL approval.\n\n";
                    $msg .= "👤 *Requester:* {$rl->requester->full_name}\n";
                    $msg .= "📝 *Subject:* \"{$rl->subject}\"\n\n";
                    $msg .= "Please review & approve here:\n";
                    $msg .= "🔗 {$link}\n\n";
                    $msg .= "_RL Monitoring System_";

                    WaService::send($director->phone, $msg);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("WA Director Error: " . $e->getMessage());
            }
        }

        // --- SKENARIO B: DIREKTUR (Level 2) APPROVE ---
        elseif ($queue->level_order == 2) {
            $rl->update(['status_flow' => 'APPROVED']);

            // -> KIRIM WA KABAR GEMBIRA KE REQUESTER
            try {
                if ($rl->requester->phone) {
                    $link = route('requisitions.show', $rl->id);

                    $msg = "*REQUEST APPROVED* ✅\n\n";
                    $msg .= "Dear {$rl->requester->full_name},\n";
                    $msg .= "Good news! Your Requisition Letter *{$rl->rl_no}* has been FULLY APPROVED by Management.\n\n";
                    $msg .= "📝 *Subject:* \"{$rl->subject}\"\n";
                    $msg .= "Status: *APPROVED* (Ready for Procurement)\n\n";
                    $msg .= "Check details:\n🔗 {$link}\n\n";
                    $msg .= "_RL Monitoring System_";

                    WaService::send($rl->requester->phone, $msg);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("WA Requester Error: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Document Approved Successfully!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:255']);
        $rl = RequisitionLetter::findOrFail($id);

        $rl->update([
            'status_flow' => 'REJECTED',
            'remark'      => $rl->remark . " [REJECT REASON: " . $request->note . "]"
        ]);

        $rl->approvalQueues()->where('approver_id', Auth::user()->employee_id)
                            ->update(['status' => 'REJECTED']);

        return redirect()->back()->with('error', 'Document has been rejected.');
    }

    public function print($id)
    {
        // Gunakan nama function 'print' agar sesuai route, atau 'printPdf' jika di route pakai printPdf
        // Di sini saya pakai 'print' sesuai route yang kita fix sebelumnya.

        $requisition = RequisitionLetter::with(['items', 'requester.department', 'approvalQueues'])
                        ->findOrFail($id);

        // A. Cari Manager
        $manager = User::where('department_id', $requisition->requester->department_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();

        // B. Cari Director (Logic ASM: Managing Director)
        $directorRole = 'Director';
        if ($requisition->company && $requisition->company->company_code == 'ASM') {
            $directorRole = 'Managing Director';
        }

        $director = User::where('company_id', $requisition->company_id)
                    ->whereHas('position', function($q) use ($directorRole) {
                        $q->where('position_name', $directorRole);
                    })->first();

        // Fallback: Jika MD kosong, cari Director biasa (Jaga-jaga)
        if (!$director) {
             $director = User::where('company_id', $requisition->company_id)
                    ->whereHas('position', function($q) {
                        $q->whereIn('position_name', ['Director', 'General Manager']);
                    })->first();
        }

        // C. Generate PDF
        $pdf = Pdf::loadView('requisitions.pdf', [
            'rl'       => $requisition,
            'manager'  => $manager,
            'director' => $director
        ]);

        $pdf->setPaper('A4', 'portrait');

        // D. Sanitasi Nama File
        $fileName = 'RL-' . str_replace(['/', '\\'], '-', $requisition->rl_no) . '.pdf';

        return $pdf->stream($fileName);
    }

public function previewTemp(Request $request)
    {
        // Validasi
        $request->validate([
            'required_date' => 'required|date',
            'subject' => 'required|string',
            'items' => 'required|array',
        ]);

        $user = Auth::user()->load(['department', 'position']);
        $company = \App\Models\Company::find($user->company_id);

        $rl = new RequisitionLetter();

        // PERUBAHAN DISINI: Ambil nomor dari input hidden, jika tidak ada pakai fallback
        $rl->rl_no = $request->input('temp_rl_no', 'DRAFT-PREVIEW');

        $rl->request_date = now();
        $rl->required_date = $request->required_date;
        $rl->subject = $request->subject;
        $rl->status_flow = 'DRAFT';
        $rl->remark = $request->remark;
        $rl->setRelation('requester', $user);
        $rl->setRelation('company', $company);

        // Items logic (Tetap sama)
        $items = collect();
        if($request->has('items')){
            foreach ($request->items as $itemData) {
                if(!empty($itemData['item_name'])) {
                    $items->push(new RequisitionItem($itemData));
                }
            }
        }
        $rl->setRelation('items', $items);

        // Logic Pejabat (Tetap sama)
        $manager = User::where('department_id', $user->department_id)
                    ->whereHas('position', fn($q) => $q->where('position_name', 'Manager'))->first();

        $directorRole = 'Director';
        if ($company && $company->company_code == 'ASM') {
            $directorRole = 'Managing Director';
        }

        $director = User::where('company_id', $user->company_id)
                    ->whereHas('position', fn($q) => $q->where('position_name', $directorRole))->first();

        if (!$director) {
             $director = User::where('company_id', $user->company_id)
                    ->whereHas('position', function($q) {
                        $q->whereIn('position_name', ['Director', 'General Manager', 'President Director']);
                    })->first();
        }

        $pdf = Pdf::loadView('requisitions.pdf', compact('rl', 'manager', 'director'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }

    // =========================================================================
    // 4. UPLOAD FILES (OPSIONAL / SUPPORT)
    // =========================================================================

    public function uploadPartial(Request $request, $id)
    {
        $request->validate(['file_partial' => 'required|mimes:pdf|max:5120']);
        $rl = RequisitionLetter::findOrFail($id);
        if ($request->hasFile('file_partial')) {
            $path = $request->file('file_partial')->store('uploads/rl_documents', 'public');
            $rl->attachment_partial = $path;
            $rl->save();
        }
        return back()->with('success', 'Document uploaded successfully!');
    }

    public function uploadFinal(Request $request, $id)
    {
        $request->validate(['file_final' => 'required|mimes:pdf|max:5120']);
        $rl = RequisitionLetter::findOrFail($id);
        if ($request->hasFile('file_final')) {
            $path = $request->file('file_final')->store('uploads/rl_documents', 'public');
            $rl->attachment_final = $path;
            $rl->save();
        }
        return back()->with('success', 'Final Document uploaded!');
    }

    public function uploadEvidence(Request $request, $id)
    {
        $request->validate(['evidence_photo' => 'required|image|max:5120']);
        $rl = RequisitionLetter::findOrFail($id);
        if ($request->hasFile('evidence_photo')) {
            $path = $request->file('evidence_photo')->store('uploads/evidence', 'public');
            $rl->evidence_photo = $path;
            $rl->status_flow = 'COMPLETED';
            $rl->save();
        }
        return back()->with('success', 'Evidence uploaded. Ticket Completed.');
    }

    // =========================================================================
    // 5. PRIVATE HELPERS
    // =========================================================================

    private function generateApprovalQueue($rl)
    {
        $rl->load('company'); // Pastikan relasi diload

        // 1. Bersihkan antrian lama
        $rl->approvalQueues()->delete();

        // 2. Cari Manager
        $manager = User::where('department_id', $rl->requester->department_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Manager');
                    })->first();

        // 3. Cari Direktur (Logic ASM)
        $directorRole = 'Director';
        if ($rl->company && $rl->company->company_code == 'ASM') {
            $directorRole = 'Managing Director';
        }

        $director = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) use ($directorRole) {
                        $q->where('position_name', $directorRole);
                    })->first();

        // Fallback: Jika spesifik role tidak ketemu, cari Director umum
        if (!$director) {
             $director = User::where('company_id', $rl->company_id)
                    ->whereHas('position', function($q) {
                        $q->where('position_name', 'Director');
                    })->first();
        }

        // 4. Masukkan ke Antrian

        // Level 1: Manager
        if ($manager) {
            $rl->approvalQueues()->create([
                'approver_id' => $manager->employee_id,
                'level_order' => 1,
                'status'      => 'PENDING'
            ]);
        }

        // Level 2: Director / MD
        if ($director) {
            $rl->approvalQueues()->create([
                'approver_id' => $director->employee_id,
                'level_order' => 2,
                'status'      => 'PENDING'
            ]);
        }
    }
}
