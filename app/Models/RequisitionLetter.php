<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionLetter extends Model
{
    use SoftDeletes;

    protected $table = 'requisition_letters';
    protected $guarded = ['id'];

    // --- RELASI KE MASTER (CROSS DB) ---
    public function requester() {
        return $this->belongsTo(User::class, 'requester_id', 'employee_id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    // --- RELASI KE TRANSAKSI (LOCAL) ---
    public function items() {
        return $this->hasMany(RequisitionItem::class, 'rl_id', 'id');
    }

    public function approvalQueues() {
        return $this->hasMany(ApprovalQueue::class, 'rl_id', 'id');
    }

    public function otpLogs() {
        return $this->hasMany(OtpAuditLog::class, 'rl_id', 'id');
    }
}
