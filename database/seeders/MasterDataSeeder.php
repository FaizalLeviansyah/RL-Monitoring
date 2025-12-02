<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\User; // Pastikan model User ada fillable 'position_id'
use Illuminate\Support\Facades\DB; // Untuk insert manual ke tbl_position
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT COMPANY & DEPT (Sama seperti sebelumnya)
        $asm = Company::firstOrCreate(['company_code' => 'ASM'], ['company_name' => 'PT Amarin Ship Management', 'logo_path' => 'logo_asm.png']);
        $deptIT = Department::firstOrCreate(['department_name' => 'IT Department', 'company_id' => $asm->company_id]);

        // 2. BUAT MASTER JABATAN (POSITION) - PENTING!
        // Kita isi tabel tbl_position milik Mas Hendri
        // Gunakan DB::table karena mungkin Bapak belum buat Model Position
        $posStaff = DB::connection('mysql_master')->table('tbl_position')->insertGetId([
            'position_name' => 'Staff',
            'company_id' => $asm->company_id
        ]);

        $posManager = DB::connection('mysql_master')->table('tbl_position')->insertGetId([
            'position_name' => 'Manager',
            'company_id' => $asm->company_id
        ]);

        $posDirector = DB::connection('mysql_master')->table('tbl_position')->insertGetId([
            'position_name' => 'Director',
            'company_id' => $asm->company_id
        ]);

        // 3. BUAT USER (Pakai position_id)

        // User Budi (Staff)
        User::firstOrCreate(
            ['email_work' => 'budi@amarin.com'],
            [
                'employee_code' => 'EMP001',
                'full_name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id,
                'department_id' => $deptIT->department_id,
                'position_id' => $posStaff, // <-- INI KUNCINYA (Angka ID)
                'phone' => '081234567890'
            ]
        );

        // User Eko (Manager)
        User::firstOrCreate(
            ['email_work' => 'eko@amarin.com'],
            [
                'employee_code' => 'EMP002',
                'full_name' => 'Eko Prasetyo',
                'password' => Hash::make('password123'),
                'company_id' => $asm->company_id,
                'department_id' => $deptIT->department_id,
                'position_id' => $posManager, // <-- Pakai ID Manager
                'phone' => '081298765432'
            ]
        );

        // ... dst untuk Director
    }
}
