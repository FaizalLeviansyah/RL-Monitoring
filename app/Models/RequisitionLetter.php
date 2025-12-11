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
     * Contoh: RL/ASM/IT/2025/XII/0001
     */
    public static function generateNumber()
    {
        $user = auth()->user();

        // 1. Ambil Kode Company & Dept
        $companyCode = optional($user->company)->company_code ?? 'GEN';
        $deptCode = strtoupper(substr(optional($user->department)->department_name ?? 'GEN', 0, 3));

        $year = date('Y');      // Contoh: 2025
        $month = date('m');     // Contoh: 12 (Bukan XII lagi)

        // 2. Cari surat terakhir pada BULAN & TAHUN ini (Reset per bulan)
        // Kenapa reset per bulan? Karena di format nomor tidak ada 'Tanggal' (Hari).
        // Kalau reset per hari tapi formatnya cuma bulan, nanti nomor 0001 bisa dobel setiap hari.
        $lastRL = self::where('company_id', $user->company_id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();

        // 3. Tentukan nomor urut
        if ($lastRL) {
            // Ambil 4 digit terakhir (substr dari belakang -4)
            $lastNumber = (int) substr($lastRL->rl_no, -4); 
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // 4. Format jadi 4 digit (0001, 0002, dst)
        $sequence = sprintf("%04d", $newNumber);

        // 5. Gabungkan dengan format Angka Bulan
        return "RL/{$companyCode}/{$deptCode}/{$year}/{$month}/{$sequence}";
    }

    protected $fillable = [
        'company_id', 'requester_id', 'rl_no', 'request_date', 
        'status_flow', 'subject', 'to_department', 'remark',
        // Tambahkan ini:
        'priority', 'required_date'
    ];
    // public static function generateNumber()
    // {
    //     $user = auth()->user();

    //     // 1. Ambil Kode Company & Dept
    //     $companyCode = optional($user->company)->company_code ?? 'GEN';
    //     $deptCode = strtoupper(substr(optional($user->department)->department_name ?? 'GEN', 0, 3));

    //     $year = date('Y');
    //     $month = date('n'); // 1-12
    //     $monthRomawi = self::getRomawi($month);

    //     // 2. Cari surat terakhir bulan ini & tahun ini
    //     $lastRL = self::where('company_id', $user->company_id)
    //                 ->whereYear('created_at', $year)
    //                 ->whereMonth('created_at', $month)
    //                 ->orderBy('id', 'desc')
    //                 ->first();

    //     // 3. Tentukan nomor urut (REVISI 4 DIGIT DISINI)
    //     if ($lastRL) {
    //         // Ambil 4 digit terakhir dari string nomor surat (Misal .../0005 -> 5)
    //         // Pastikan offset substr benar (-4 untuk 4 digit)
    //         $lastNumber = (int) substr($lastRL->rl_no, -4); 
    //         $newNumber = $lastNumber + 1;
    //     } else {
    //         $newNumber = 1;
    //     }

    //     // 4. Format jadi 4 digit (0001, 0002, dst)
    //     $sequence = sprintf("%04d", $newNumber);

    //     // 5. Gabungkan
    //     return "RL/{$companyCode}/{$deptCode}/{$year}/{$monthRomawi}/{$sequence}";
    // }

    // // Helper Ubah Angka ke Romawi
    // private static function getRomawi($month) {
    //     $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
    //     return $map[$month];
    // }
}