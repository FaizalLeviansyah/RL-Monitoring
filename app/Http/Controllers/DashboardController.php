<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionLetter;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // --- LOGIC HITUNG STATISTIK (Berdasarkan Requester) ---

        // 1. Total Surat Saya
        $myTotal = RequisitionLetter::where('requester_id', $user->employee_id)->count();

        // 2. Menunggu (On Progress / Draft)
        $myPending = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->whereIn('status_flow', ['ON_PROGRESS', 'DRAFT'])->count();

        // 3. Disetujui
        $myApproved = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'APPROVED')->count();

        // 4. Ditolak
        $myRejected = RequisitionLetter::where('requester_id', $user->employee_id)
                        ->where('status_flow', 'REJECTED')->count();

        // Kirim variabel ke View dashboard
        return view('dashboard', compact('myTotal', 'myPending', 'myApproved', 'myRejected'));
    }
}
