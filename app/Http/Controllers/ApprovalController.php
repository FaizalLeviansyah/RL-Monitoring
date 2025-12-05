<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalQueue;
use App\Models\OtpAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    // 1. REQUEST OTP (Dipanggil saat klik Approve)
    public function requestOtp(Request $request)
    {
        $queue = ApprovalQueue::findOrFail($request->queue_id);
        $user = Auth::user();

        // Security Check: Pastikan yang request adalah Approver asli
        if ($queue->approver_id != $user->employee_id) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }

        // Generate Kode OTP 6 Digit
        $otpCode = rand(100000, 999999);

        // Simpan Hash OTP ke Database Queue (Biar aman)
        $queue->update([
            'otp_code' => bcrypt($otpCode), // Kita hash biar database admin gak bisa intip
            'method' => 'ONLINE_OTP'
        ]);

        // Simpan Log Audit (Disini kita simpan OTP mentah buat DEBUGGING development aja)
        // Nanti kalau sudah Production, OTP JANGAN disimpan di log, langsung kirim WA.
        OtpAuditLog::create([
            'rl_id' => $queue->rl_id,
            'user_id' => $user->employee_id,
            'action' => 'REQUEST_OTP_APPROVE',
            'otp_sent_to' => $user->phone, // No HP Manager
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            // 'debug_otp' => $otpCode // (Opsional: Buat nyontek saat testing)
        ]);

        // TODO: Panggil API WhatsApp Disini (Nanti)
        // WhatsAppService::send($user->phone, "Kode OTP Anda: $otpCode");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to WhatsApp!',
            'debug_otp' => $otpCode // SEMENTARA: Kita kirim balik ke browser biar Bapak bisa copy-paste
        ]);
    }

    // 2. VERIFIKASI OTP & APPROVE
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'queue_id' => 'required',
            'otp_input' => 'required|numeric'
        ]);

        $queue = ApprovalQueue::with('letter')->findOrFail($request->queue_id);

        // Cek Apakah OTP Cocok?
        if (!password_verify($request->otp_input, $queue->otp_code)) {
            return response()->json(['error' => 'Invalid OTP Code!'], 422);
        }

        // --- OTP BENAR: PROSES APPROVAL ---

        // 1. Update Status Queue Manager jadi APPROVED
        $queue->update([
            'status' => 'APPROVED',
            'approved_at' => now()
        ]);

        // 2. Cek Apakah Ada Level Selanjutnya? (Direktur)
        // Logikanya: Kalau Level 1 selesai, buat antrian Level 2
        // (Disini kita simplifikasi dulu: Kalau Manager Approve -> Dokumen Selesai/Approved)

        // Update Status Surat Utama
        $queue->letter->update(['status_flow' => 'APPROVED']);
        // Jika ada direktur, ubah status_flow jadi 'ON_PROGRESS' lagi, dan create queue baru.

        // Simpan Log Sukses
        OtpAuditLog::create([
            'rl_id' => $queue->rl_id,
            'user_id' => Auth::user()->employee_id,
            'action' => 'OTP_VERIFIED_APPROVED',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true, 'message' => 'Document Approved Successfully!']);
    }
}
