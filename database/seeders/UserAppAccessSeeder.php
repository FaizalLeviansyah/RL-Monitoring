<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserApplicationAccess;

class UserAppAccessSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            'budi@amarin.com',
            'eko@amarin.com',
            'admin@amarin.group',
            // 'bos@amarin.com' // Uncomment jika user bos sudah dibuat di MasterDataSeeder
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
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
