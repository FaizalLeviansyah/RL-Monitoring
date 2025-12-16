<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requisition_letters', function (Blueprint $table) {
            // Cek dulu biar gak error kalau dijalankan ulang
            if (!Schema::hasColumn('requisition_letters', 'attachment_partial')) {
                $table->string('attachment_partial')->nullable()->after('remark');
            }
            if (!Schema::hasColumn('requisition_letters', 'attachment_final')) {
                $table->string('attachment_final')->nullable()->after('attachment_partial');
            }
            if (!Schema::hasColumn('requisition_letters', 'evidence_photo')) {
                $table->string('evidence_photo')->nullable()->after('attachment_final');
            }
        });
    }

    public function down()
    {
        Schema::table('requisition_letters', function (Blueprint $table) {
            $table->dropColumn(['attachment_partial', 'attachment_final', 'evidence_photo']);
        });
    }
};