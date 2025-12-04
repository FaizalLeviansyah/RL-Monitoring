<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Import Model Lain
use App\Models\Position;
use App\Models\Company;
use App\Models\Department;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mysql_master';
    protected $table = 'tbl_employee';
    protected $primaryKey = 'employee_id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'employee_code',
        'full_name',
        'email_work',
        'password',
        'company_id',
        'department_id',
        'position_id', // <--- WAJIB DITAMBAHKAN (Tadi kurang ini)
        'phone',
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
    public function position() {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }
    // Fungsi bawaan login (jika perlu)
    public function getEmailForPasswordReset() {
        return $this->email_work;
    }
}
