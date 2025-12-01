<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // --- KONEKSI KE DATABASE MASTER ---
    protected $connection = 'mysql_master';
    protected $table = 'tbl_employee';
    protected $primaryKey = 'employee_id';

    // Agar timestamp mengikuti standar Mas Hendri (created_at, updated_at)
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'employee_code',
        'full_name',
        'email_work',
        'password',
        'company_id',
        'department_id',
        'phone',
        'role', // Jika ada kolom role
        'is_deleted'
    ];

    protected $hidden = [
        'password',
    ];

    // --- RELASI ---
    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}
