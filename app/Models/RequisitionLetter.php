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
    /**
     * FUNGSI GENERATE NOMOR SURAT OTOMATIS
     * Format: RL/COMPANY/DEPT/TAHUN/BULAN/URUTAN
     * Contoh: RL/ASM/IT/2025/XII/001
     */
    public static function generateNumber()
    {
        $user = auth()->user();

        // 1. Ambil Kode Company & Dept (Dari relasi user)
        // Kita pakai optional() untuk jaga-jaga kalau datanya null
        $companyCode = optional($user->company)->company_code ?? 'GEN';
        // Ambil 3 huruf pertama departemen, misal IT, HRD, FIN
        $deptCode = strtoupper(substr(optional($user->department)->department_name ?? 'GEN', 0, 3));

        $year = date('Y');
        $month = date('n'); // 1-12
        $monthRomawi = self::getRomawi($month);

        // 2. Cari urutan terakhir bulan ini untuk Company ini
        $lastRL = self::where('company_id', $user->company_id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();

        // 3. Tentukan nomor urut
        if ($lastRL) {
            // Ambil 3 digit terakhir dari nomor surat terakhir
            $lastNumber = (int) substr($lastRL->rl_no, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // 4. Format jadi 3 digit (001, 002, dst)
        $sequence = sprintf("%03d", $newNumber);

        // 5. Gabungkan
        return "RL/{$companyCode}/{$deptCode}/{$year}/{$monthRomawi}/{$sequence}";
    }

    // Helper Ubah Angka ke Romawi
    private static function getRomawi($month) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$month];
    }
}
