<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- PENTING: Tambahkan ini untuk jaga-jaga

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CARA A: MENGGUNAKAN FITUR LARAVEL STANDARD
        // (Kadang gagal untuk ENUM jika Doctrine DBAL tidak support)
        /*
        Schema::table('requisition_letters', function (Blueprint $table) {
            $table->enum('status_flow', [
                'DRAFT',
                'ON_PROGRESS',
                'PARTIALLY_APPROVED', // <--- Item Baru
                'APPROVED',
                'REJECTED',
                'WAITING_SUPPLY',
                'COMPLETED'
            ])->change();
        });
        */

        // CARA B: RAW SQL (LEBIH DISARANKAN UNTUK ENUM)
        // Ini lebih aman dan pasti berhasil untuk mengubah kolom ENUM
        DB::statement("ALTER TABLE requisition_letters MODIFY COLUMN status_flow ENUM('DRAFT', 'ON_PROGRESS', 'PARTIALLY_APPROVED', 'APPROVED', 'REJECTED', 'WAITING_SUPPLY', 'COMPLETED') DEFAULT 'DRAFT'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke daftar enum lama jika di-rollback
        DB::statement("ALTER TABLE requisition_letters MODIFY COLUMN status_flow ENUM('DRAFT', 'ON_PROGRESS', 'APPROVED', 'REJECTED', 'WAITING_SUPPLY', 'COMPLETED') DEFAULT 'DRAFT'");
    }
};
