<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\UserApplicationAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['company', 'department', 'position']);

        // 1. Filter by Company (PT)
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // 2. Search by Name/Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                  ->orWhere('email_work', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        $companies = Company::all(); // Untuk dropdown filter

        return view('admin.users.index', compact('users', 'companies'));
    }

    public function create()
    {
        $companies = Company::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('admin.users.create', compact('companies', 'departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email_work' => 'required|email|unique:mysql_master.tbl_employee,email_work',
            'company_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
            'password' => 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan ke MASTER DATABASE (tbl_employee)
            $user = User::create([
                'employee_code' => 'MAN-' . rand(1000,9999), // Auto generate code
                'full_name' => $request->full_name,
                'email_work' => $request->email_work,
                'password' => Hash::make($request->password),
                'company_id' => $request->company_id,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'phone' => $request->phone,
                'employment_status' => 'Active'
            ]);

            // 2. Beri Akses Login ke Aplikasi Ini (TRANSACTION DATABASE)
            UserApplicationAccess::create([
                'user_id' => $user->employee_id,
                'app_code' => 'RL-MONITORING',
                'is_active' => true
            ]);
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil didaftarkan & diberi akses!');
    }

    // (Method Edit & Update bisa ditambahkan dengan pola serupa)
}