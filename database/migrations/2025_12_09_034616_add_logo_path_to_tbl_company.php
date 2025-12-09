<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            // Cek dulu: Kalau kolom 'logo_path' BELUM ADA, baru ditambahkan
            if (!Schema::connection('mysql_master')->hasColumn('tbl_company', 'logo_path')) {
                Schema::connection('mysql_master')->table('tbl_company', function (Blueprint $table) {
                    $table->string('logo_path', 255)->nullable()->after('company_name');
                });
            }
        }

    public function down(): void
    {
        Schema::connection('mysql_master')->table('tbl_company', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
