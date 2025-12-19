<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisition_items', function (Blueprint $table) {

            $table->unsignedBigInteger('master_item_id')->nullable();

            $table->text('specification')->nullable();

            $table->foreign('master_item_id')->references('id')->on('master_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            // Cek dulu array foreign key, jika error saat rollback, hapus baris dropForeign ini
            $table->dropForeign(['master_item_id']);
            $table->dropColumn(['master_item_id', 'specification']);
        });
    }
};
