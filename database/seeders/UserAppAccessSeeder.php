<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserApplicationAccess;

class UserAppAccessSeeder extends Seeder
{
    public function run(): void
    {
        // DAFTAR EMAIL YANG BOLEH LOGIN (Sesuai MasterDataSeeder)
        $users = [
            'admin@amarin.com',      // Super Admin
            'hendri@amarin.com',     // Manager IT
            'faizal@amarin.com',     // Staff IT
            'dinesh@amarin.com',     // Direktur
            'crewing1@amarin.com',   // Staff ACS
            'logistik1@caraka.com',  // Staff CTP (Domain Caraka)
        ];

        foreach ($users as $email) {
            $user = User::where('email_work', $email)->first();

            if ($user) {
                UserApplicationAccess::updateOrCreate(
                    [
                        'user_id' => $user->employee_id,
                        'app_code' => 'RL-MONITORING'
                    ],
                    [
                        'is_active' => true,
                        'valid_until' => null
                    ]
                );
            }
        }
    }
}
