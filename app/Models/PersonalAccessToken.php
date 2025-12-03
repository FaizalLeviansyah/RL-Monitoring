<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // Paksa model ini agar selalu menggunakan koneksi 'mysql' (Database Transaksi)
    // Jangan ikut-ikutan User yang pakai 'mysql_master'
    protected $connection = 'mysql';

    protected $table = 'personal_access_tokens';
}
