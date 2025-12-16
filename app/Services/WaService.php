<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// ... namespace & imports

class WaService
{
    public static function send($nomerTujuan, $pesan)
    {
        // 1. Normalisasi Nomor (Sama seperti sebelumnya)
        $nomer = preg_replace('/[^0-9]/', '', $nomerTujuan);
        if (substr($nomer, 0, 1) === '0') {
            $nomer = '62' . substr($nomer, 1);
        }

        if (empty($nomer)) {
            return ['status' => false, 'msg' => 'Nomor HP tujuan kosong/salah format.'];
        }
        
        // 2. Kirim API (DENGAN BYPASS SSL)
        try {
            // TAMBAHAN: withoutVerifying() sangat penting untuk Localhost!
            $response = Http::withoutVerifying()->get('https://wa.amarin.cloud/message/send-text', [
                'session' => 'notif',
                'to'      => $nomer,
                'text'    => $pesan
            ]);

            if ($response->successful()) {
                return ['status' => true, 'msg' => 'Notifikasi WA terkirim ke ' . $nomer];
            } else {
                // Log error dari API (misal session not found)
                Log::error('WA API Error: ' . $response->body());
                return ['status' => false, 'msg' => 'Gagal koneksi ke server WA.'];
            }

        } catch (\Exception $e) {
            Log::error('WA Exception: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'Error sistem WA: ' . $e->getMessage()];
        }
    }
}
