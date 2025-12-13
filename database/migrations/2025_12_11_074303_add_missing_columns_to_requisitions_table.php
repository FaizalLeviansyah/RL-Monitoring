<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah Kolom di Tabel Header (Surat)
        Schema::table('requisition_letters', function (Blueprint $table) {
            // Cek dulu biar gak error kalau dijalankan ulang
            if (!Schema::hasColumn('requisition_letters', 'to_department')) {
                $table->string('to_department')->nullable()->default('Purchasing / Procurement')->after('subject');
            }
            if (!Schema::hasColumn('requisition_letters', 'priority')) {
                $table->string('priority')->default('Normal')->after('to_department');
            }
            if (!Schema::hasColumn('requisition_letters', 'required_date')) {
                $table->date('required_date')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('requisition_letters', 'remark')) {
                $table->text('remark')->nullable()->after('required_date');
            }
        });

        // 2. Tambah Kolom di Tabel Item (Barang)
        Schema::table('requisition_items', function (Blueprint $table) {
            if (!Schema::hasColumn('requisition_items', 'part_number')) {
                $table->string('part_number')->nullable()->after('description');
            }
            if (!Schema::hasColumn('requisition_items', 'stock_on_hand')) {
                $table->integer('stock_on_hand')->default(0)->after('uom');
            }
        });
    }
    public function down()
    {
        Schema::table('requisition_letters', function (Blueprint $table) {
            $table->dropColumn(['to_department', 'priority', 'required_date', 'remark']);
        });

        Schema::table('requisition_items', function (Blueprint $table) {
            $table->dropColumn(['part_number', 'stock_on_hand']);
        });
    }
};
