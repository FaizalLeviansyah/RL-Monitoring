<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil User yang sedang login (dari database Master)
        $user = Auth::user();

        // --- LOGIC HITUNG-HITUNGAN (STATS) ---

        // 1. Total Semua Request Saya
        $myTotal = RequisitionLetter::where('requester_id', $user->employee_id)->count();

        // 2. Yang Masih 'On Progress' (Menunggu Approval)
        $myPending = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'ON_PROGRESS')->count();

        // 3. Yang Sudah Selesai/Approved
        $myApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'APPROVED')->count();

        // 4. Yang Ditolak
        $myRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'REJECTED')->count();

        // Kirim data ini ke tampilan (View)
        return view('dashboard', compact('myTotal', 'myPending', 'myApproved', 'myRejected'));
    }
}
