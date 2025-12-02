<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserApplicationAccess;

class UserAppAccessSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil User berdasarkan email
        $budi = User::where('email_work', 'budi@amarin.com')->first();
        $eko = User::where('email_work', 'eko@amarin.com')->first();
        $bos = User::where('email_work', 'bos@amarin.com')->first();

        // Beri Akses ke Aplikasi 'RL-MONITORING'

        if ($budi) {
            UserApplicationAccess::create([
                'user_id' => $budi->employee_id,
                'app_code' => 'RL-MONITORING',
                'is_active' => true
            ]);
        }

        if ($eko) {
            UserApplicationAccess::create([
                'user_id' => $eko->employee_id,
                'app_code' => 'RL-MONITORING',
                'is_active' => true
            ]);
        }

        if ($bos) {
            UserApplicationAccess::create([
                'user_id' => $bos->employee_id,
                'app_code' => 'RL-MONITORING',
                'is_active' => true
            ]);
        }
    }
}
