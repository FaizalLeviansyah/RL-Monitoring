<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. BERSIHKAN DATA LAMA (RESET TOTAL) ---
        Schema::connection('mysql_master')->disableForeignKeyConstraints();

        DB::connection('mysql_master')->table('tbl_employee')->truncate();
        DB::connection('mysql_master')->table('tbl_department')->truncate();
        DB::connection('mysql_master')->table('tbl_position')->truncate();
        DB::connection('mysql_master')->table('tbl_company')->truncate();

        Schema::connection('mysql_master')->enableForeignKeyConstraints();


        // --- 2. BUAT COMPANY (PT) ---
        $asm = Company::create(['company_code' => 'ASM', 'company_name' => 'ASM (Amarin Ship Management)', 'logo_path' => 'Logo_PT_ASM.jpg']);
        $acs = Company::create(['company_code' => 'ACS', 'company_name' => 'ACS (Amarin Crewing Services)', 'logo_path' => 'Logo_PT_ACS.png']);
        $ctp = Company::create(['company_code' => 'CTP', 'company_name' => 'CTP (Caraka Tirta Pratama)', 'logo_path' => 'Logo_PT_CTP.jpg']);


        // --- 3. BUAT DEPARTEMEN ---

        // A. PT ASM
        $deptsASM = [
            'IT (Information Technology)',
            'HR (Human-Resource)',
            'Finance',
            'Technical',
            'Marine',
            'Purchasing'
        ];
        foreach ($deptsASM as $deptName) {
            Department::create(['company_id' => $asm->company_id, 'department_name' => $deptName]);
        }

        // B. PT ACS
        $deptsACS = [
            'HR (Human-Resource)',
            'Finance',
            'Crewing Operation',
            'IT (Information Technology)'
        ];
        foreach ($deptsACS as $deptName) {
            Department::create(['company_id' => $acs->company_id, 'department_name' => $deptName]);
        }

        // C. PT CTP (UPDATED: Added IT Department)
        $deptsCTP = [
            'HR (Human-Resource)',
            'Finance',
            'Administration',
            'Logistik',
            'Claim',
            'IT (Information Technology)' // <--- BARU: Request Bapak
        ];
        foreach ($deptsCTP as $deptName) {
            Department::create(['company_id' => $ctp->company_id, 'department_name' => $deptName]);
        }


        // --- 4. BUAT POSISI (JABATAN) ---
        $positions = ['Staff', 'Manager', 'Director', 'Super Admin'];
        foreach ([$asm, $acs, $ctp] as $company) {
            foreach ($positions as $posName) {
                DB::connection('mysql_master')->table('tbl_position')->insert([
                    'position_name' => $posName,
                    'company_id' => $company->company_id,
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }


        // --- 5. BUAT USER (SAMPLE) ---
        $defaultPass = Hash::make('AmarinCaraka1234');

        // Super Admin (Di ASM)
        $posSuperAdminASM = $this->getPosId($asm->company_id, 'Super Admin');
        $deptIT_ASM = Department::where('company_id', $asm->company_id)->where('department_name', 'like', 'IT%')->first()->department_id;

        $this->createUser('SA001', 'IT Super Admin', 'admin@amarin.com', $asm->company_id, $deptIT_ASM, $posSuperAdminASM, $defaultPass);

        // Manager IT ASM (Hendri)
        $posMgrASM = $this->getPosId($asm->company_id, 'Manager');
        $this->createUser('ASM001', 'Hendri Setiawan', 'hendri@amarin.com', $asm->company_id, $deptIT_ASM, $posMgrASM, $defaultPass);

        // Staff IT ASM (Faizal)
        $posStaffASM = $this->getPosId($asm->company_id, 'Staff');
        $this->createUser('ASM002', 'Faizal Leviansyah', 'faizal@amarin.com', $asm->company_id, $deptIT_ASM, $posStaffASM, $defaultPass);

        // Direktur ASM (Dinesh) - Dept Marine
        $deptMarine = Department::where('company_id', $asm->company_id)->where('department_name', 'Marine')->first()->department_id;
        $posDirASM = $this->getPosId($asm->company_id, 'Director');
        $this->createUser('ASM003', 'Capt. Dinesh', 'dinesh@amarin.com', $asm->company_id, $deptMarine, $posDirASM, $defaultPass);
    }

    // --- HELPER ---
    private function getPosId($companyId, $name) {
        return DB::connection('mysql_master')->table('tbl_position')
            ->where('company_id', $companyId)->where('position_name', $name)->value('position_id');
    }

    private function createUser($code, $name, $email, $compId, $deptId, $posId, $pass) {
        User::create([
            'employee_code' => $code, 'full_name' => $name, 'email_work' => $email, 'password' => $pass,
            'company_id' => $compId, 'department_id' => $deptId, 'position_id' => $posId,
            'phone' => '08123456789', 'employment_status' => 'Active'
        ]);
    }
}
