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
        // PERBAIKAN: Tambahkan ->where('is_deleted', 0)
        $query = User::with(['company', 'department', 'position'])
                     ->where('is_deleted', 0); // <--- INI KUNCINYA

        // Filter by Company (PT)
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search by Name/Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                  ->orWhere('email_work', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $companies = Company::all();

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
        // Validasi & Logic Store (Sama seperti sebelumnya)
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email_work' => 'required|email|unique:mysql_master.tbl_employee,email_work',
            'password' => 'required|string|min:8',
            'company_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'employee_code' => 'MAN-' . rand(1000,9999),
                'full_name' => $request->full_name,
                'email_work' => $request->email_work,
                'password' => Hash::make($request->password),
                'company_id' => $request->company_id,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'phone' => $request->phone,
                'employment_status' => 'Active',
                'is_deleted' => 0
            ]);

            UserApplicationAccess::create([
                'user_id' => $user->employee_id,
                'app_code' => 'RL-MONITORING',
                'is_active' => true
            ]);
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    // --- PASTIKAN METHOD INI ADA & DI DALAM CLASS ---
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $companies = Company::all();
        $departments = Department::all();
        $positions = Position::all();

        return view('admin.users.edit', compact('user', 'companies', 'departments', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email_work' => 'required|email|unique:mysql_master.tbl_employee,email_work,'.$id.',employee_id',
            'company_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
        ]);

        DB::transaction(function () use ($request, $user) {
            $dataToUpdate = [
                'full_name' => $request->full_name,
                'email_work' => $request->email_work,
                'phone' => $request->phone,
                'company_id' => $request->company_id,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'employment_status' => $request->employment_status,
            ];

            if ($request->filled('password')) {
                $dataToUpdate['password'] = Hash::make($request->password);
            }
            $user->update($dataToUpdate);
        });

        return redirect()->route('admin.users.index')->with('success', 'User updated!');
    }
    public function destroy($id)
        {
            $user = User::findOrFail($id);

            DB::transaction(function () use ($user) {
                // 1. Matikan Akses Login
                UserApplicationAccess::where('user_id', $user->employee_id)
                    ->update(['is_active' => false]);

                // 2. Tandai User sebagai Terhapus (Soft Delete Manual)
                $user->update([
                    'employment_status' => 'Resigned',
                    'is_deleted' => 1 // <--- Ini yang membuat dia hilang dari index nanti
                ]);
            });

            return redirect()->route('admin.users.index')->with('success', 'User berhasil dinonaktifkan.');
        }
    }
