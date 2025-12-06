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
        // Kita pakai Schema::connection karena ini tabel Master
        Schema::connection('mysql_master')->table('tbl_employee', function (Blueprint $table) {
            $table->string('profile_photo_path', 2048)->nullable()->after('email_work');
            $table->string('signature_path', 2048)->nullable()->after('profile_photo_path'); // Saran: Sekalian buat TTD Digital
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_master')->table('tbl_employee', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_path', 'signature_path']);
        });
    }
};
