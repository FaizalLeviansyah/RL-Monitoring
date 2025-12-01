<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplicationAccess extends Model
{
    protected $table = 'user_application_access';
    protected $guarded = ['id'];

    // Relasi Cross-Database ke User Master
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'employee_id');
    }
}
