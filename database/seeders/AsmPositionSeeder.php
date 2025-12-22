<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class AsmPositionSeeder extends Seeder
{
    public function run()
    {
        // 1. Cari ID PT ASM
        // Pastikan Model Company sudah menggunakan koneksi yang benar.
        // Jika model Company belum diset connection-nya, kita tembak manual koneksinya di sini:
        $asm = DB::connection('mysql_master')->table('tbl_company')
                 ->where('company_code', 'ASM')
                 ->first();

        if ($asm) {
            $positions = [
                'Managing Director',
                'Deputy Managing Director',
                'Director',
                'General Manager',
                'Manager',
                'Superintendent',
                'Supervisor',
                'Staff'
            ];

            foreach ($positions as $posName) {
                // 2. Cek di database MASTER (gunakan connection 'mysql_master')
                $exists = DB::connection('mysql_master')->table('tbl_position')
                            ->where('company_id', $asm->company_id)
                            ->where('position_name', $posName)
                            ->exists();

                if (!$exists) {
                    // 3. Insert ke database MASTER
                    DB::connection('mysql_master')->table('tbl_position')->insert([
                        'position_name' => $posName,
                        'company_id'    => $asm->company_id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
            $this->command->info('Posisi ASM berhasil ditambahkan ke Database Master!');
        } else {
            $this->command->error('PT ASM tidak ditemukan di tabel tbl_company.');
        }
    }
}
