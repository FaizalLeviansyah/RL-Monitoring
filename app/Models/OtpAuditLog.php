<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpAuditLog extends Model
{
    protected $table = 'otp_audit_logs';
    protected $guarded = ['id'];

    public function letter() {
        return $this->belongsTo(RequisitionLetter::class, 'rl_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'employee_id');
    }
}
