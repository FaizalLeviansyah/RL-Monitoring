<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use App\Models\User; // <--- Pastikan Model User di-import
use Illuminate\Support\Facades\Auth;

class DepartmentActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $positionName = $user->position->position_name ?? '';

        // Query Dasar: Filter Company ID (Wajib)
        $query = RequisitionLetter::with(['requester.department', 'company', 'approvalQueues'])
                    ->where('company_id', $user->company_id);

        // --- LOGIC ISOLASI (CROSS DATABASE FIX) ---

        // KASUS 1: STAFF & MANAGER
        // Hanya melihat aktivitas di DEPARTEMEN yang sama
        if ($positionName == 'Staff' || $positionName == 'Manager') {

            // LANGKAH 1: Ambil semua employee_id yang satu departemen dengan user login
            // (Query ini akan otomatis lari ke db_master_amarin karena Model User settingannya mysql_master)
            $colleagueIds = User::where('department_id', $user->department_id)
                                ->pluck('employee_id');

            // LANGKAH 2: Filter RL berdasarkan ID tersebut di database transaksi
            $query->whereIn('requester_id', $colleagueIds);
        }

        // KASUS 2: DIREKTUR
        // Tidak perlu filter tambahan, dia otomatis melihat semua RL di company_id tersebut.

        // Tambahan: Filter Pencarian (Opsional)
        if ($request->filled('search')) {
            $query->where('rl_no', 'like', '%'.$request->search.'%');
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('activities.department_index', compact('activities', 'user'));
    }
}
