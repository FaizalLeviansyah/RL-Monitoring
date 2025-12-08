<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT COMPANY & DEPT
        $asm = Company::firstOrCreate(
            ['company_code' => 'ASM'],
            ['company_name' => 'PT Amarin Ship Management', 'logo_path' => 'logo_asm.png']
        );

        $deptIT = Department::firstOrCreate(
            ['department_name' => 'IT Department', 'company_id' => $asm->company_id],
            ['description' => 'Information Technology']
        );

        // 2. BUAT MASTER JABATAN (POSITION)
        $posStaff = $this->createPosition($asm->company_id, 'Staff');
        $posManager = $this->createPosition($asm->company_id, 'Manager');
        $posDirector = $this->createPosition($asm->company_id, 'Director');
        
        // --- TAMBAHAN BARU (SUPER ADMIN) ---
        $posSuperAdmin = $this->createPosition($asm->company_id, 'Super Admin');

        // 3. BUAT USER
        // User Budi (Staff)
        User::updateOrCreate(
            ['email_work' => 'budi@amarin.com'],
            [
                'employee_code' => 'EMP001',
                'full_name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id,
                'department_id' => $deptIT->department_id,
                'position_id' => $posStaff,
                'phone' => '081234567890',
                'employment_status' => 'Active'
            ]
        );

        // User Eko (Manager)
        User::updateOrCreate(
            ['email_work' => 'eko@amarin.com'],
            [
                'employee_code' => 'EMP002',
                'full_name' => 'Eko Prasetyo',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id,
                'department_id' => $deptIT->department_id,
                'position_id' => $posManager,
                'phone' => '081298765432',
                'employment_status' => 'Active'
            ]
        );

        // 4. BUAT USER SUPER ADMIN (DIMASUKKAN KE SINI)
        User::updateOrCreate(
            ['email_work' => 'admin@amarin.group'], // Email khusus
            [
                'employee_code' => 'SA001',
                'full_name' => 'IT Super Administrator',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id, // Base di ASM
                'department_id' => $deptIT->department_id,
                'position_id' => $posSuperAdmin, // Kuncinya disini
                'phone' => '08129999999',
                'employment_status' => 'Active'
            ]
        );

    } // <--- BATAS AKHIR FUNGSI RUN (Jangan taruh kode di bawah ini)

    // Helper untuk Cek dulu sebelum Insert Position
    private function createPosition($companyId, $name)
    {
        $existing = DB::connection('mysql_master')->table('tbl_position')
            ->where('company_id', $companyId)
            ->where('position_name', $name)
            ->value('position_id');

        if ($existing) {
            return $existing;
        }

        return DB::connection('mysql_master')->table('tbl_position')->insertGetId([
            'position_name' => $name,
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}