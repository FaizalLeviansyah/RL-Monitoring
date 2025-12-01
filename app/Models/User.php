<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // KONEKSI KE DATABASE MASTER
    protected $connection = 'mysql_master';
    protected $table = 'tbl_employee';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_code', 'full_name', 'email_work', 'password',
        'company_id', 'department_id', 'phone', 'role'
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi (Contoh)
    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
