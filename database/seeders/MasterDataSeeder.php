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
        // 1. UPDATE DATA COMPANY (Format Nama Lengkap)
        $asm = Company::updateOrCreate(
            ['company_code' => 'ASM'],
            ['company_name' => 'ASM (Amarin Ship Management)', 'logo_path' => 'logo_asm.png']
        );

        $acs = Company::updateOrCreate(
            ['company_code' => 'ACS'],
            ['company_name' => 'ACS (Amarin Crewing Services)', 'logo_path' => 'logo_acs.png']
        );

        $ctp = Company::updateOrCreate(
            ['company_code' => 'CTP'],
            ['company_name' => 'CTP (Caraka Tirta Pratama)', 'logo_path' => 'logo_ctp.png']
        );

        // 2. UPDATE DEPARTEMEN SESUAI REQUEST

        // A. Departemen ASM (Sesuai Capture Excel Bapak - Saya isi default umum dulu, silakan tambah)
        $deptsASM = ['IT', 'HR', 'Finance', 'Technical', 'Marine', 'Purchasing'];
        foreach ($deptsASM as $deptName) {
            Department::firstOrCreate(['company_id' => $asm->company_id, 'department_name' => $deptName]);
        }

        // B. Departemen ACS (Amarin Crewing Services)
        $deptsACS = ['HR', 'Finance', 'Crewing Operation', 'TI'];
        foreach ($deptsACS as $deptName) {
            Department::firstOrCreate(['company_id' => $acs->company_id, 'department_name' => $deptName]);
        }

        // C. Departemen CTP (Caraka Tirta Pratama)
        $deptsCTP = ['HR (Human Resource)', 'Finance', 'Administration', 'Logistik', 'Claim'];
        foreach ($deptsCTP as $deptName) {
            Department::firstOrCreate(['company_id' => $ctp->company_id, 'department_name' => $deptName]);
        }

        // 3. BUAT POSISI (JABATAN)
        // Kita butuh posisi ini untuk dropdown user nanti
        $positions = ['Staff', 'Manager', 'Director', 'Super Admin'];

        // Loop untuk membuat posisi di setiap PT (Agar fleksibel)
        foreach ([$asm, $acs, $ctp] as $company) {
            foreach ($positions as $posName) {
                $this->createPosition($company->company_id, $posName);
            }
        }

        // 4. SUPER ADMIN USER (Default di ASM)
        $posSuperAdminASM = $this->createPosition($asm->company_id, 'Super Admin');

        User::updateOrCreate(
            ['email_work' => 'admin@amarin.group'],
            [
                'employee_code' => 'SA001',
                'full_name' => 'IT Super Administrator',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id,
                'department_id' => Department::where('company_id', $asm->company_id)->where('department_name', 'IT')->first()->department_id,
                'position_id' => $posSuperAdminASM,
                'phone' => '08129999999',
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
