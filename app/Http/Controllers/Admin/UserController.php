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
use Illuminate\Validation\Rules; // Tambahkan import Rules

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['company', 'department', 'position'])
                     ->where('is_deleted', 0);

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
        $request->validate([
            'full_name' => 'required|string|max:255',
            // Pastikan koneksi master di sini juga
            'email_work' => 'required|email|unique:mysql_master.tbl_employee,email_work',
            'password' => 'required|string|min:8',
            'company_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
            'phone' => 'nullable|string|max:20' // Tambahkan validasi phone
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
                'phone' => $request->phone, // Pastikan input name di form create adalah 'phone' atau mapping manual
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

    public function edit(User $user)
    {
        $companies = Company::all();
        $departments = Department::all();
        $positions = Position::all();

        return view('admin.users.edit', compact('user', 'companies', 'departments', 'positions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',

            // PERBAIKAN UTAMA: Tambahkan 'mysql_master.' sebelum nama tabel
            'email_work' => 'required|string|email|max:255|unique:mysql_master.tbl_employee,email_work,' . $user->employee_id . ',employee_id',

            'phone_number' => 'required|string|max:20',
            'company_id' => 'required|exists:mysql_master.tbl_company,company_id', // Sesuaikan connection jika perlu
            'department_id' => 'required|exists:mysql_master.tbl_department,department_id',
            'position_id' => 'required|exists:mysql_master.tbl_position,position_id',
            'role' => 'nullable|string',
        ]);

        $dataToUpdate = [
            'full_name' => $request->full_name,
            'email_work' => $request->email_work,
            'phone' => $request->phone_number, // Mapping input 'phone_number' ke db 'phone'
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'employment_status' => $request->employment_status // Update status juga
        ];

        // Cek password
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
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
                'is_deleted' => 1
            ]);
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dinonaktifkan.');
    }
}
