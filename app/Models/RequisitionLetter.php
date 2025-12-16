<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionLetter extends Model
{
    use SoftDeletes;

    protected $table = 'requisition_letters';

    // --- PERBAIKAN: HAPUS $fillable, GUNAKAN HANYA $guarded ---
    // Ini artinya: "Lindungi kolom ID, sisanya BOLEH diisi semua (termasuk attachment)"
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

    /**
     * FUNGSI GENERATE NOMOR SURAT OTOMATIS
     */
    public static function generateNumber()
    {
        $user = auth()->user();

        $companyCode = optional($user->company)->company_code ?? 'GEN';
        $deptCode = strtoupper(substr(optional($user->department)->department_name ?? 'GEN', 0, 3));

        $year = date('Y');
        $month = date('m');

        $lastRL = self::where('company_id', $user->company_id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastRL) {
            $lastNumber = (int) substr($lastRL->rl_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $sequence = sprintf("%04d", $newNumber);

        return "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$sequence}";
    }
}
