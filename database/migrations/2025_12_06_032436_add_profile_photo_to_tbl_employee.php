<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            // Cek dulu: Kalau kolom 'profile_photo_path' BELUM ADA, baru ditambahkan
            if (!Schema::connection('mysql_master')->hasColumn('tbl_employee', 'profile_photo_path')) {
                Schema::connection('mysql_master')->table('tbl_employee', function (Blueprint $table) {
                    $table->string('profile_photo_path', 2048)->nullable()->after('email_work');
                    $table->string('signature_path', 2048)->nullable()->after('profile_photo_path');
                });
            }
        }

    public function down(): void
    {
        Schema::connection('mysql_master')->table('tbl_employee', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_path', 'signature_path']);
        });
    }
};
