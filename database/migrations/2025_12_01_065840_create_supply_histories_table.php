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
        Schema::create('supply_histories', function (Blueprint $table) {
            $table->id();
            // FK ke Item Barang
            $table->foreignId('rl_item_id')->constrained('requisition_items')->onDelete('cascade');

            // FK ke Master (Siapa yang terima barang)
            $table->integer('received_by');

            $table->integer('qty_received');
            $table->string('photo_proof', 255)->nullable(); // Foto barang datang

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_histories');
    }
};
