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
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            
            // FK ke Tabel Surat (Sesama Transaksi, boleh pakai constrained)
            $table->foreignId('rl_id')->constrained('requisition_letters')->onDelete('cascade');
            
            $table->string('item_name', 255);
            $table->integer('qty');
            $table->string('uom', 50); // Unit/Pcs
            
            // Opsional khusus PT ASM
            $table->string('item_type', 50)->nullable(); 
            $table->text('description')->nullable();
            
            // Status per barang (Waiting -> Supplied)
            $table->enum('status_item', ['WAITING', 'SUPPLIED'])->default('WAITING');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};
