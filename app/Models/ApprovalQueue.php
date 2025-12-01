<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalQueue extends Model
{
    protected $table = 'approval_queues';
    protected $guarded = ['id'];

    public function letter() {
        return $this->belongsTo(RequisitionLetter::class, 'rl_id', 'id');
    }

    // Relasi ke User Master (Penyetuju)
    public function approver() {
        return $this->belongsTo(User::class, 'approver_id', 'employee_id');
    }
}
