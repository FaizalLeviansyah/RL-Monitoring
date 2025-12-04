<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter; // <--- WAJIB ADA
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil User yang sedang login
        $user = Auth::user();

        // --- LOGIC HITUNG STATISTIK (KARTU ATAS) ---
        $myTotal = RequisitionLetter::where('requester_id', $user->employee_id)->count();

        $myPending = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->whereIn('status_flow', ['ON_PROGRESS', 'DRAFT'])->count();

        $myApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'APPROVED')->count();

        $myRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'REJECTED')->count();

        // --- LOGIC TABEL HISTORY (YANG MUNGKIN ERROR TADI) ---
        $recent_rls = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

        // --- KIRIM SEMUA VARIABEL KE VIEW ---
        return view('dashboard', compact(
            'myTotal',
            'myPending',
            'myApproved',
            'myRejected',
            'recent_rls'
        ));
    }
}
