<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequisitionLetter;
use App\Models\Company;
use Illuminate\Http\Request;

class GlobalMonitoringController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar (Ambil Semua RL dengan Relasi)
        $query = RequisitionLetter::with(['company', 'requester.department', 'requester.position']);

        // 2. Filter Pencarian & Sorting
        if ($request->filled('search')) {
            $query->where('rl_no', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status_flow', $request->status);
        }

        // 3. Ambil Data (Pagination)
        $activities = $query->latest()->paginate(20)->withQueryString();

        // Data untuk Filter Dropdown
        $companies = Company::all();

        return view('admin.monitoring.index', compact('activities', 'companies'));
    }
}
