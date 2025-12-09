<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Position; // Pastikan Model Position diimport
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 0. BERSIHKAN DATA LAMA (TRUNCATE) ---
        // Kita matikan Foreign Key Check dulu biar bisa truncate tanpa error
        Schema::connection('mysql_master')->disableForeignKeyConstraints();

        // Kosongkan tabel Master agar tidak ada duplikat (RESET TOTAL)
        // Hati-hati: Ini akan menghapus semua user/dept lama di database master
        DB::connection('mysql_master')->table('tbl_employee')->truncate();
        DB::connection('mysql_master')->table('tbl_department')->truncate();
        DB::connection('mysql_master')->table('tbl_position')->truncate();
        DB::connection('mysql_master')->table('tbl_company')->truncate();

        Schema::connection('mysql_master')->enableForeignKeyConstraints();

        // --- 1. BUAT COMPANY ---
        $asm = Company::create(['company_code' => 'ASM', 'company_name' => 'ASM (Amarin Ship Management)', 'logo_path' => 'Logo_PT_ASM.jpg']);
        $acs = Company::create(['company_code' => 'ACS', 'company_name' => 'ACS (Amarin Crewing Services)', 'logo_path' => 'Logo_PT_ACS.png']);
        $ctp = Company::create(['company_code' => 'CTP', 'company_name' => 'CTP (Caraka Tirta Pratama)', 'logo_path' => 'Logo_PT_CTP.jpg']);

        // --- 2. BUAT DEPARTEMEN (Sesuai Request) ---

        // A. PT ASM
        $deptsASM = ['IT (Information Technology)', 'HR (Human-Resource)', 'Finance', 'Technical', 'Marine', 'Purchasing'];
        foreach ($deptsASM as $deptName) {
            Department::create(['company_id' => $asm->company_id, 'department_name' => $deptName]);
        }

        // B. PT ACS
        $deptsACS = ['HR (Human-Resource)', 'Finance', 'Crewing Operation', 'IT (Information Technology)'];
        foreach ($deptsACS as $deptName) {
            Department::create(['company_id' => $acs->company_id, 'department_name' => $deptName]);
        }

        // C. PT CTP
        $deptsCTP = ['HR (Human-Resource)', 'Finance', 'Administration', 'Logistik', 'Claim'];
        foreach ($deptsCTP as $deptName) {
            Department::create(['company_id' => $ctp->company_id, 'department_name' => $deptName]);
        }

        // --- 3. BUAT POSISI (JABATAN) ---
        $positions = ['Staff', 'Manager', 'Director', 'Super Admin'];
        foreach ([$asm, $acs, $ctp] as $company) {
            foreach ($positions as $posName) {
                // Gunakan Helper atau Create langsung
                DB::connection('mysql_master')->table('tbl_position')->insert([
                    'position_name' => $posName,
                    'company_id' => $company->company_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // --- 4. BUAT USER SUPER ADMIN ---
        // Ambil ID Posisi Super Admin di ASM
        $posSuperAdminASM = DB::connection('mysql_master')->table('tbl_position')
            ->where('company_id', $asm->company_id)
            ->where('position_name', 'Super Admin')
            ->value('position_id');

        // Ambil ID Dept IT di ASM
        $deptIT_ASM = Department::where('company_id', $asm->company_id)
            ->where('department_name', 'like', 'IT%') // Pakai like biar aman
            ->first()
            ->department_id;

        User::create([
            'employee_code' => 'SA001',
            'full_name' => 'IT Super Administrator',
            'email_work' => 'admin@amarin.group',
            'password' => Hash::make('password123'),
            'company_id' => $asm->company_id,
            'department_id' => $deptIT_ASM,
            'position_id' => $posSuperAdminASM,
            'phone' => '08129999999',
            'employment_status' => 'Active'
        ]);

        // --- 5. BUAT USER DUMMY LAIN (Budi & Eko) ---
        $posStaffASM = DB::connection('mysql_master')->table('tbl_position')->where('company_id', $asm->company_id)->where('position_name', 'Staff')->value('position_id');
        $posMgrASM = DB::connection('mysql_master')->table('tbl_position')->where('company_id', $asm->company_id)->where('position_name', 'Manager')->value('position_id');

        User::create([
            'employee_code' => 'EMP001', 'full_name' => 'Budi Santoso', 'email_work' => 'budi@amarin.com',
            'password' => Hash::make('password123'), 'company_id' => $asm->company_id, 'department_id' => $deptIT_ASM,
            'position_id' => $posStaffASM, 'phone' => '081234567890', 'employment_status' => 'Active'
        ]);

        User::create([
            'employee_code' => 'EMP002', 'full_name' => 'Eko Prasetyo', 'email_work' => 'eko@amarin.com',
            'password' => Hash::make('password123'), 'company_id' => $asm->company_id, 'department_id' => $deptIT_ASM,
            'position_id' => $posMgrASM, 'phone' => '081298765432', 'employment_status' => 'Active'
        ]);
    }
}
