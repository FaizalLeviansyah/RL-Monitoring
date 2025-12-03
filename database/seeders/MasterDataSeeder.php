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
        // Kita buat fungsi helper kecil biar kodenya rapi & tidak error duplicate
        $posStaff = $this->createPosition($asm->company_id, 'Staff');
        $posManager = $this->createPosition($asm->company_id, 'Manager');
        $posDirector = $this->createPosition($asm->company_id, 'Director');

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
    }

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
